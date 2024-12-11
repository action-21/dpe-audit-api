<?php

namespace App\Domain\PlancherBas\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Service\Interpolation;
use App\Domain\PlancherBas\Data\{BpbRepository, Upb0Repository, UeRepository, UpbRepository};
use App\Domain\PlancherBas\Enum\{EtatIsolation, Mitoyennete, TypePlancherBas};
use App\Domain\PlancherBas\PlancherBas;
use App\Domain\PlancherBas\ValueObject\Performance;
use App\Domain\Simulation\Simulation;

/**
 * @uses \App\Domain\PlancherBas\Service\MoteurSurfaceDeperditive
 * @uses \App\Domain\Lnc\Service\MoteurPerformance
 */
final class MoteurPerformance
{
    // Lambda par défaut des planchers bas isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.042;

    public function __construct(
        private BpbRepository $b_repository,
        private Upb0Repository $u0_repository,
        private UpbRepository $u_repository,
        private UeRepository $ue_repository,
        private Interpolation $interpolation,
    ) {}

    public function calcule_performance(PlancherBas $entity, Simulation $simulation): ?Performance
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return null;

        $b = $this->b(
            mitoyennete: $entity->mitoyennete(),
            b_lnc: $entity->local_non_chauffe()?->performance()->b(isolation_paroi: $entity->est_isole())
        );
        $upb0 = $this->upb0(
            type_plancher_bas: $entity->caracteristique()->type,
            upb0_saisi: $entity->caracteristique()->u0,
        );
        $upb = $this->upb(
            upb0: $upb0,
            zone_climatique: $simulation->zone_climatique(),
            effet_joule: $simulation->effet_joule(),
            annee_construction: $entity->annee_construction_defaut(),
            etat_isolation: $entity->isolation()->etat_isolation,
            annee_isolation: $entity->isolation()->annee_isolation,
            epaisseur_isolation: $entity->isolation()->epaisseur_isolation,
            resistance_thermique_isolation: $entity->isolation()->resistance_thermique_isolation,
            upb_saisi: $entity->caracteristique()->u,
        );
        $upb_final = $this->upb_final(
            mitoyennete: $entity->mitoyennete(),
            annee_construction: $entity->annee_construction_defaut(),
            surface: $entity->caracteristique()->surface,
            perimetre: $entity->caracteristique()->perimetre,
            upb: $upb,
            upb0: $upb0,
        );

        $dp = $this->dp(
            mitoyennete: $entity->mitoyennete(),
            u: $upb_final,
            b: $b,
            sdep: $entity->surface_deperditive(),
        );

        return Performance::create(b: $b, u0: $upb0, u: $upb_final, dp: $dp);
    }

    /**
     * Déperdition thermique en W/K
     */
    public function dp(Mitoyennete $mitoyennete, float $sdep, float $u, float $b,): float
    {
        return $mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL ? 0 : $u * $b * $sdep;
    }

    /**
     * Coefficient de transmission thermique de la paroi non isolée en W/m.K
     * 
     * @param null|float $upb0_saisi - Coefficient de transmission thermique connu et justifié de la paroi non isolée en W/m.K
     */
    public function upb0(TypePlancherBas $type_plancher_bas, ?float $upb0_saisi): float
    {
        if ($upb0_saisi)
            return $upb0_saisi;

        if (null === $data = $this->u0_repository->find_by(type_plancher_bas: $type_plancher_bas))
            throw new \DomainException("Valeur forfaitaire Upb0 non trouvée");

        return $data->u0;
    }

    /**
     * Coefficient de transmission thermique en W/m.K
     * 
     * @param float $upb0 - Coefficient de transmission thermique de la paroi non isolée en W/m.K
     * @param null|int $epaisseur_isolation - Épaisseur de l'isolant en mm
     * @param null|float $resistance_thermique_isolation - Résistance thermique de l'isolant connu et justifié en m².K/W
     * @param null|float $upb_saisi - Coefficient de transmission thermique connu et justifié en W/m.K
     */
    public function upb(
        float $upb0,
        ZoneClimatique $zone_climatique,
        bool $effet_joule,
        int $annee_construction,
        EtatIsolation $etat_isolation,
        ?int $annee_isolation,
        ?int $epaisseur_isolation,
        ?float $resistance_thermique_isolation,
        ?float $upb_saisi,
    ): float {
        if ($upb_saisi)
            return $upb_saisi;

        if ($etat_isolation === EtatIsolation::NON_ISOLE)
            return $upb0;

        if ($etat_isolation === EtatIsolation::ISOLE) {
            if ($resistance_thermique_isolation)
                return 1 / (1 / $upb0 + $resistance_thermique_isolation);
            if ($epaisseur_isolation)
                return 1 / (1 / $upb0 + $epaisseur_isolation / 1000 / self::LAMBDA_ISOLATION_DEFAUT);

            $annee_isolation = $annee_isolation ?? ($annee_construction <= 1974 ? 1975 : $annee_construction);
        }

        if (null === $upb = $this->u_repository->find_by(
            zone_climatique: $zone_climatique,
            annee_construction_isolation: $annee_isolation ?? $annee_construction,
            effet_joule: $effet_joule,
        )) throw new \DomainException("Valeur forfaitaire Upb non trouvée");

        return $upb->upb;
    }

    public function ue(
        Mitoyennete $mitoyennete,
        int $annee_construction,
        float $surface,
        float $perimetre,
        float $upb,
    ): float {
        $collection = $this->ue_repository
            ->search_by(mitoyennete: $mitoyennete, annee_construction: $annee_construction)
            ->valeurs_proches(surface: $surface, perimetre: $perimetre);

        if ($collection->isEmpty())
            throw new \DomainException("Valeur forfaitaire Ue non trouvée");

        if ($collection->count() === 1)
            return $collection->first()->ue;

        return $this->interpolation->interpolation_lineaire(
            x: $upb,
            x1: $collection->first()->upb,
            x2: $collection->last()->upb,
            y1: $collection->first()->ue,
            y2: $collection->last()->ue,
        );
    }

    public function upb_final(
        Mitoyennete $mitoyennete,
        int $annee_construction,
        float $surface,
        float $perimetre,
        float $upb,
        float $upb0,
    ): float {
        $upb_final = \in_array($mitoyennete, [
            Mitoyennete::TERRE_PLEIN,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::SOUS_SOL_NON_CHAUFFE
        ]) ? $this->ue(
            mitoyennete: $mitoyennete,
            annee_construction: $annee_construction,
            surface: $surface,
            perimetre: $perimetre,
            upb: $upb,
        ) : $upb;

        return \min($upb0, $upb_final);
    }

    /**
     * Coefficient de réduction des déperditions thermiques
     * 
     * @param null|float $b_lnc - Coefficient de réduction des déperditions thermiques du local non chauffé mitoyen
     */
    public function b(Mitoyennete $mitoyennete, ?float $b_lnc): float
    {
        if (null !== $b_lnc)
            return $b_lnc;

        if (null === $valeur = $this->b_repository->find_by(mitoyennete: $mitoyennete))
            throw new \DomainException("Valeur forfaitaire b non trouvée");

        return $valeur->b;
    }
}
