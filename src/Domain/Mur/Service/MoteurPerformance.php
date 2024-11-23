<?php

namespace App\Domain\Mur\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Service\Interpolation;
use App\Domain\Mur\Data\{BmurRepository, Umur0Repository, UmurRepository};
use App\Domain\Mur\Enum\{EtatIsolation, Mitoyennete, TypeDoublage, TypeMur};
use App\Domain\Mur\Mur;
use App\Domain\Mur\ValueObject\Performance;
use App\Domain\Simulation\Simulation;

/**
 * @uses \App\Domain\Lnc\Service\MoteurPerformance
 */
final class MoteurPerformance
{
    // Lambda par défaut des murs isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.04;

    // Résistance additionnelle dûe à la présence d'un enduit sur une paroi ancienne
    final public const RESISTANCE_ENDUIT_PAROI_ANCIENNE = 0.7;

    public function __construct(
        private BmurRepository $b_repository,
        private Umur0Repository $u0_repository,
        private UmurRepository $u_repository,
        private Interpolation $interpolation,
    ) {}

    public function calcule_performance(Mur $entity, Simulation $simulation): ?Performance
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return null;

        $b = $this->b(
            mitoyennete: $entity->mitoyennete(),
            b_lnc: $entity->local_non_chauffe()?->performance()->b(isolation_paroi: $entity->est_isole())
        );
        $umur0 = $this->umur0(
            type_mur: $entity->caracteristique()->type,
            epaisseur_mur: $entity->caracteristique()->epaisseur_defaut(),
            type_doublage: $entity->caracteristique()->type_doublage,
            paroi_ancienne: $entity->caracteristique()->paroi_ancienne,
            presence_enduit_isolant: $entity->caracteristique()->presence_enduit_isolant,
            umur0_saisi: $entity->caracteristique()->u0,
        );
        $umur = $this->umur(
            umur0: $umur0,
            zone_climatique: $simulation->zone_climatique(),
            effet_joule: $simulation->effet_joule(),
            annee_construction: $entity->annee_construction_defaut(),
            etat_isolation: $entity->isolation()->etat_isolation,
            annee_isolation: $entity->isolation()->annee_isolation,
            epaisseur_isolation: $entity->isolation()->epaisseur_isolation,
            resistance_thermique_isolation: $entity->isolation()->resistance_thermique_isolation,
            umur_saisi: $entity->caracteristique()->u,
        );
        $dp = $this->dp(
            mitoyennete: $entity->mitoyennete(),
            u: $umur,
            b: $b,
            sdep: $entity->surface_deperditive(),
        );

        return Performance::create(b: $b, u0: $umur0, u: $umur, dp: $dp);
    }

    public function dp(Mitoyennete $mitoyennete, float $sdep, float $u, float $b,): float
    {
        return $mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL ? 0 : $u * $b * $sdep;
    }

    public function umur0(
        TypeMur $type_mur,
        int|float $epaisseur_mur,
        TypeDoublage $type_doublage,
        bool $paroi_ancienne,
        bool $presence_enduit_isolant,
        ?float $umur0_saisi,
    ): float {
        if ($umur0_saisi)
            return $umur0_saisi;

        // length compris entre 0 et 2
        $collection = $this->u0_repository
            ->search_by(type_mur: $type_mur)
            ->valeurs_proches(epaisseur: $epaisseur_mur);

        if (0 === $collection->count())
            throw new \DomainException('Valeur forfaitaire U0 non trouvée');

        if (1 === $collection->count()) {
            $u0 = $collection->first()->u0 + $this->u0_enduit_isolant(
                paroi_ancienne: $paroi_ancienne,
                presence_enduit_isolant: $presence_enduit_isolant
            ) + $this->u0_doublage(type_doublage: $type_doublage);
        } else {
            $u0 = $this->interpolation->interpolation_lineaire(
                x: $epaisseur_mur,
                x1: $collection->first()->epaisseur,
                x2: $collection->last()->epaisseur,
                y1: $collection->first()->u0,
                y2: $collection->last()->u0,
            ) + $this->u0_enduit_isolant(
                paroi_ancienne: $paroi_ancienne,
                presence_enduit_isolant: $presence_enduit_isolant
            ) + $this->u0_doublage(type_doublage: $type_doublage);
        }

        return \min($u0, 2.5);
    }

    public function u0_enduit_isolant(bool $paroi_ancienne, bool $presence_enduit_isolant): float
    {
        return $paroi_ancienne && $presence_enduit_isolant ? 1 / self::RESISTANCE_ENDUIT_PAROI_ANCIENNE : 0;
    }

    public function u0_doublage(TypeDoublage $type_doublage): float
    {
        return ($r_doublage = $type_doublage->resistance_thermique_doublage()) > 0 ? 1 / $r_doublage : 0;
    }

    public function umur(
        float $umur0,
        ZoneClimatique $zone_climatique,
        bool $effet_joule,
        int $annee_construction,
        EtatIsolation $etat_isolation,
        ?int $annee_isolation,
        ?int $epaisseur_isolation,
        ?float $resistance_thermique_isolation,
        ?float $umur_saisi,
    ): float {
        if ($umur_saisi)
            return $umur_saisi;

        if ($etat_isolation === EtatIsolation::NON_ISOLE)
            return $umur0;

        if ($etat_isolation === EtatIsolation::ISOLE) {
            if ($resistance_thermique_isolation)
                return 1 / (1 / $umur0 + $resistance_thermique_isolation);
            if ($epaisseur_isolation)
                return 1 / (1 / $umur0 + $epaisseur_isolation / 1000 / self::LAMBDA_ISOLATION_DEFAUT);

            $annee_isolation = $annee_isolation ?? ($annee_construction <= 1974 ? 1975 : $annee_construction);
        }

        if (null === $u = $this->u_repository->find_by(
            zone_climatique: $zone_climatique,
            annee_construction_isolation: $annee_isolation ?? $annee_construction,
            effet_joule: $effet_joule,
        )) throw new \DomainException('Valeur forfaitaire U non trouvée');

        return \min($umur0, $u->u);
    }

    public function b(Mitoyennete $mitoyennete, ?float $b_lnc): float
    {
        if (null !== $b_lnc)
            return $b_lnc;

        if (null === $valeur = $this->b_repository->find_by(mitoyennete: $mitoyennete))
            throw new \DomainException("Valeur forfaitaire b non trouvée");

        return $valeur->b;
    }
}
