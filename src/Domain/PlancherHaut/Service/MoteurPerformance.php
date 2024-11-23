<?php

namespace App\Domain\PlancherHaut\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Error\DomainError;
use App\Domain\PlancherHaut\Data\{BphRepository, Uph0Repository, UphRepository};
use App\Domain\PlancherHaut\Enum\{Categorie, EtatIsolation, Mitoyennete, TypePlancherHaut};
use App\Domain\PlancherHaut\PlancherHaut;
use App\Domain\PlancherHaut\ValueObject\Performance;
use App\Domain\Simulation\Simulation;

/**
 * @uses \App\Domain\PlancherHaut\Service\MoteurPerformance
 * @uses \App\Domain\Lnc\Service\MoteurPerformance
 */
final class MoteurPerformance
{
    // Lambda par défaut des planchers hauts isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.04;

    public function __construct(
        private BphRepository $b_repository,
        private Uph0Repository $u0_repository,
        private UphRepository $u_repository,
    ) {}

    public function calcule_performance(PlancherHaut $entity, Simulation $simulation): ?Performance
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return null;

        $b = $this->b(
            mitoyennete: $entity->mitoyennete(),
            b_lnc: $entity->local_non_chauffe()?->performance()->b(isolation_paroi: $entity->est_isole())
        );
        $uph0 = $this->uph0(
            type_plancher_haut: $entity->caracteristique()->type,
            uph0_saisi: $entity->caracteristique()->u0,
        );
        $uph = $this->uph(
            uph0: $uph0,
            zone_climatique: $simulation->zone_climatique(),
            effet_joule: $simulation->effet_joule(),
            annee_construction: $entity->annee_construction_defaut(),
            categorie: $entity->categorie(),
            etat_isolation: $entity->isolation()->etat_isolation,
            annee_isolation: $entity->isolation()->annee_isolation,
            epaisseur_isolation: $entity->isolation()->epaisseur_isolation,
            resistance_thermique_isolation: $entity->isolation()->resistance_thermique_isolation,
            uph_saisi: $entity->caracteristique()->u,
        );
        $dp = $this->dp(
            mitoyennete: $entity->mitoyennete(),
            u: $uph,
            b: $b,
            sdep: $entity->surface_deperditive(),
        );

        return Performance::create(b: $b, u0: $uph0, u: $uph, dp: $dp);
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
     * @param null|float $uph0_saisi - Coefficient de transmission thermique connu et justifié de la paroi non isolée en W/m.K
     */
    public function uph0(TypePlancherHaut $type_plancher_haut, ?float $uph0_saisi): float
    {
        if ($uph0_saisi)
            return $uph0_saisi;

        if (null === $valeur = $this->u0_repository->find_by(type_plancher_haut: $type_plancher_haut))
            DomainError::valeur_forfaitaire("U0");

        return $valeur->u0;
    }

    /**
     * Coefficient de transmission thermique en W/m.K
     * 
     * @param float $uph0 - Coefficient de transmission thermique de la paroi non isolée en W/m.K
     * @param null|int $epaisseur_isolation - Épaisseur de l'isolant en mm
     * @param null|float $resistance_thermique_isolation - Résistance thermique de l'isolant connu et justifié en m².K/W
     * @param null|float $uph_saisi - Coefficient de transmission thermique connu et justifié en W/m.K
     */
    public function uph(
        float $uph0,
        ZoneClimatique $zone_climatique,
        bool $effet_joule,
        int $annee_construction,
        Categorie $categorie,
        EtatIsolation $etat_isolation,
        ?int $annee_isolation,
        ?int $epaisseur_isolation,
        ?float $resistance_thermique_isolation,
        ?float $uph_saisi,
    ): float {
        if ($uph_saisi)
            return $uph_saisi;

        if ($etat_isolation === EtatIsolation::NON_ISOLE)
            return $uph0;

        if ($etat_isolation === EtatIsolation::ISOLE) {
            if ($resistance_thermique_isolation)
                return 1 / (1 / $uph0 + $resistance_thermique_isolation);
            if ($epaisseur_isolation)
                return 1 / (1 / $uph0 + $epaisseur_isolation / 1000 / self::LAMBDA_ISOLATION_DEFAUT);

            $annee_isolation = $annee_isolation ?? ($annee_construction <= 1974 ? 1975 : $annee_construction);
        }

        if (null === $u = $this->u_repository->find_by(
            zone_climatique: $zone_climatique,
            categorie: $categorie,
            annee_construction_isolation: $annee_isolation ?? $annee_construction,
            effet_joule: $effet_joule,
        )) throw new \DomainException('Valeur forfaitaire U non trouvée');

        return \min($uph0, $u->u);
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
