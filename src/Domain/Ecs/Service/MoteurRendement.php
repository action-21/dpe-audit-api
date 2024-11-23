<?php

namespace App\Domain\Ecs\Service;

use App\Domain\Common\Enum\{Enum, ScenarioUsage, ZoneClimatique};
use App\Domain\Ecs\Data\{FecsRepository, RdRepository, RgRepository};
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\{BouclageReseau, CategorieGenerateur, EnergieGenerateur, IsolationReseau, LabelGenerateur, TypeGenerateur, UsageEcs};
use App\Domain\Ecs\ValueObject\{Rendement, RendementCollection};

/**
 * @uses \App\Domain\Ecs\Service\MoteurPerformance
 * @uses \App\Domain\Ecs\Service\MoteurPerte
 */
final class MoteurRendement
{
    public function __construct(
        private FecsRepository $fecs_repository,
        private RdRepository $rd_repository,
        private RgRepository $rg_repository,
    ) {}

    public function calcule_rendement(Systeme $entity): RendementCollection
    {
        $fecs = $this->fecs(
            type_batiment: $entity->installation()->ecs()->audit()->type_batiment(),
            zone_climatique: $entity->installation()->ecs()->audit()->zone_climatique(),
            annee_installation: $entity->installation()->ecs()->audit()->annee_construction_batiment(),
            usage_systeme_solaire: $entity->installation()->solaire()?->usage,
            fecs_saisi: $entity->installation()->solaire()?->fecs,
        );
        $rd = $this->rd(
            reseau_collectif: $entity->generateur()->generateur_collectif(),
            bouclage_reseau: $entity->reseau()->type_bouclage,
            alimentation_contigue: $entity->reseau()->alimentation_contigues,
            production_volume_habitable: $entity->generateur()->position_volume_chauffe(),
        );
        $rg = [$this->rg(
            type_generateur: $entity->generateur()->type(),
            energie_generateur: $entity->generateur()->energie(),
        )];

        $rgs = [$entity->generateur()->performance()->cop ?? 1];
        $rgs[] = $this->rgs(
            type_generateur: $entity->generateur()->type(),
            energie_generateur: $entity->generateur()->energie(),
            isolation_reseau: $entity->reseau()->isolation_reseau,
        );

        $collection = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            $becs = $entity->ecs()->besoins()->besoins(scenario: $scenario);
            $pertes_stockage = $entity->pertes_stockage()->pertes(scenario: $scenario);
            $pertes_stockage += $entity->generateur()->pertes_stockage()->pertes(scenario: $scenario);

            $rs = $this->rs(
                type_generateur: $entity->generateur()->type(),
                energie_generateur: $entity->generateur()->energie(),
                label_generateur: $entity->generateur()->signaletique()->label,
                pertes_stockages: $pertes_stockage,
                rd: $rd,
                becs: $becs,
            );
            $rg[] = $this->rg_combustion(
                type_generateur: $entity->generateur()->type(),
                energie_generateur: $entity->generateur()->energie(),
                becs: $becs,
                rpn: $entity->generateur()->performance()->rpn ?? 0,
                qp0: $entity->generateur()->performance()->qp0 ?? 0,
                pveilleuse: $entity->generateur()->performance()->pveilleuse ?? 0,
            );
            $rgs[] = $this->rgs_combustion(
                type_generateur: $entity->generateur()->type(),
                energie_generateur: $entity->generateur()->energie(),
                becs: $becs,
                pertes_stockage: $pertes_stockage,
                rpn: $entity->generateur()->performance()->rpn ?? 0,
                qp0: $entity->generateur()->performance()->qp0 ?? 0,
                pveilleuse: $entity->generateur()->performance()->pveilleuse ?? 0,
            );

            $iecs = $this->iecs(\min($rg), \min($rgs), $rd, $rs);

            $collection[] = Rendement::create(
                scenario: $scenario,
                fecs: $fecs,
                iecs: $iecs,
                rd: $rd,
                rs: $rs,
                rg: \min($rg),
                rgs: \min($rgs),
            );
        }
        return new RendementCollection($collection);
    }

    public function iecs(float ...$rendements): float
    {
        return 1 / array_product($rendements);
    }

    public function fecs(
        Enum $type_batiment,
        ZoneClimatique $zone_climatique,
        int $annee_installation,
        ?UsageEcs $usage_systeme_solaire,
        ?float $fecs_saisi,
    ): float {
        if ($fecs_saisi)
            return $fecs_saisi;
        if (null === $usage_systeme_solaire)
            return 0;

        $anciennete_installation = ((int) (new \DateTime())->format('Y')) - $annee_installation;

        if (null === $fecs = $this->fecs_repository->find_by(
            type_batiment: $type_batiment,
            zone_climatique: $zone_climatique,
            usage_systeme_solaire: $usage_systeme_solaire,
            anciennete_installation: $anciennete_installation,
        )) throw new \RuntimeException('Valeur forfaitaire Fecs non trouvée');

        return $fecs->fecs;
    }

    public function production_ecs_solaire(float $becs, float $fecs, float $iecs,): float
    {
        return $becs * $fecs * $iecs;
    }

    /**
     * Rendement annuel de distribution
     */
    public function rd(
        bool $reseau_collectif,
        ?BouclageReseau $bouclage_reseau,
        ?bool $alimentation_contigue,
        ?bool $production_volume_habitable,
    ): float {
        if (null === $data = $this->rd_repository->find_by(
            reseau_collectif: $reseau_collectif,
            bouclage_reseau: $bouclage_reseau,
            alimentation_contigue: $alimentation_contigue,
            production_volume_habitable: $production_volume_habitable,
        )) throw new \RuntimeException('Valeur forfaitaire Rd non trouvée');

        return $data->rd;
    }

    /**
     * Rendement annuel de stockage
     * 
     * @param float $pertes_stockages - Pertes annuelles de stockages en Wh
     * @param float $becs - Besoin annuel d'eau chaude sanitaire en kWh
     */
    public function rs(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        float $pertes_stockages,
        float $rd,
        float $becs,
        ?LabelGenerateur $label_generateur,
    ): float {
        $categorie_generateur = CategorieGenerateur::determine(type: $type_generateur, energie: $energie_generateur,);

        if (false === $this->rs_applicable($categorie_generateur))
            return 1;
        if ($type_generateur === TypeGenerateur::BALLON_ELECTRIQUE_VERTICAL && $label_generateur === LabelGenerateur::NE_PERFORMANCE_C)
            return 1.08 / (1 + ($pertes_stockages * $rd) / ($becs * 1000));

        return 1 / (1 + ($pertes_stockages * $rd) / ($becs * 1000));
    }

    public function rs_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_ELECTRIQUE,
            CategorieGenerateur::CHAUFFE_EAU_ELECTRIQUE,
            CategorieGenerateur::CHAUFFE_EAU_INSTANTANE,
        ]);
    }

    /**
     * Rendement annuel de génération/stockage
     */
    public function rgs(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        IsolationReseau $isolation_reseau,
    ): float {
        $categorie_generateur = CategorieGenerateur::determine(type: $type_generateur, energie: $energie_generateur,);

        return $this->rgs_applicable($categorie_generateur) ? match ($isolation_reseau) {
            IsolationReseau::ISOLE => 0.9,
            IsolationReseau::NON_ISOLE, IsolationReseau::INCONNU => 0.75,
        } : 1;
    }

    public function rgs_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_MULTI_BATIMENT,
            CategorieGenerateur::RESEAU_CHALEUR,
        ]);
    }

    /**
     * Rendement annuel de génération/stockage
     * 
     * @param float $becs - Besoin annuel d'eau chaude sanitaire (Wh)
     * @param float $pertes_stockage - Pertes annuelles de stockage (Wh)
     * @param float $rpn - Rendement à pleine charge (%)
     * @param float $qp0 - Pertes à l'arrêt (W)
     * @param float $pveilleuse - Puissance de la veilleuse (W)
     */
    public function rgs_combustion(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        float $becs,
        float $pertes_stockage,
        float $rpn,
        float $qp0,
        float $pveilleuse,
    ): float {
        $categorie_generateur = CategorieGenerateur::determine(type: $type_generateur, energie: $energie_generateur,);

        if (false === $this->rgs_combustion_applicable($categorie_generateur))
            return 1;

        return $categorie_generateur === CategorieGenerateur::ACCUMULATEUR
            ? 1 / ((1 / $rpn) + ((8592 * $qp0 + $pertes_stockage) / $becs) + (6970 * ($pveilleuse / $becs)))
            : 1 / ((1 / $rpn) + ((1790 * $qp0 + $pertes_stockage) / $becs) + (6970 * ((0.5 * $pveilleuse) / $becs)));
    }

    public function rgs_combustion_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::ACCUMULATEUR,
            CategorieGenerateur::CHAUDIERE_BOIS,
            CategorieGenerateur::CHAUDIERE_STANDARD,
            CategorieGenerateur::CHAUDIERE_BASSE_TEMPERATURE,
            CategorieGenerateur::CHAUDIERE_CONDENSATION,
            CategorieGenerateur::POELE_BOIS_BOUILLEUR,
        ]);
    }

    /**
     * Rendement annuel de génération par effet joule
     */
    public function rg(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur,): float
    {
        $categorie_generateur = CategorieGenerateur::determine(type: $type_generateur, energie: $energie_generateur,);

        return $this->rg_applicable($categorie_generateur) ? match ($categorie_generateur) {
            CategorieGenerateur::CHAUDIERE_ELECTRIQUE => 0.97,
            CategorieGenerateur::CHAUFFE_EAU_ELECTRIQUE => 0.9,
        } : 1;
    }

    public function rg_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return \in_array($categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_ELECTRIQUE,
            CategorieGenerateur::CHAUFFE_EAU_ELECTRIQUE,
        ]);
    }

    /**
     * Rendement annuel de génération par combustion
     * 
     * @param float $becs - Besoin annuel d'eau chaude sanitaire (Wh)
     * @param float $rpn - Rendement à pleine charge (%)
     * @param float $qp0 - Pertes à l'arrêt (W)
     * @param float $pveilleuse - Puissance de la veilleuse (W)
     */
    public function rg_combustion(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        float $becs,
        float $rpn,
        float $qp0,
        float $pveilleuse,
    ): float {
        $categorie_generateur = CategorieGenerateur::determine(type: $type_generateur, energie: $energie_generateur,);

        return $this->rg_combustion_applicable($categorie_generateur)
            ? 1 / ((1 / $rpn) + (1790 * ($qp0 / $becs)) + (6970 * ($pveilleuse / $becs)))
            : 1;
    }

    public function rg_combustion_applicable(CategorieGenerateur $categorie_generateur,): bool
    {
        return $categorie_generateur === CategorieGenerateur::CHAUFFE_EAU_INSTANTANE;
    }
}
