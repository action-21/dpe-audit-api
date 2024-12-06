<?php

namespace App\Domain\Ecs\Service;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Ecs\Data\CrRepository;
use App\Domain\Ecs\Entity\{Generateur, Systeme};
use App\Domain\Ecs\Enum\{LabelGenerateur, TypeGenerateur, TypePerte, UsageEcs};
use App\Domain\Ecs\ValueObject\{Perte, PerteCollection};
use App\Domain\Simulation\Simulation;

/**
 * @uses \App\Domain\Ecs\Service\MoteurDimensionnement
 */
final class MoteurPerte
{
    public function __construct(
        private MoteurDimensionnement $moteur_dimensionnement,
        private CrRepository $cr_repository,
    ) {}

    public function calcule_pertes_distribution(Systeme $entity, Simulation $simulation): PerteCollection
    {
        return PerteCollection::create(function (ScenarioUsage $scenario, Mois $mois) use ($entity, $simulation) {
            $becs = $entity->ecs()->besoins()->besoins(scenario: $scenario, mois: $mois);
            $nref = $simulation->audit()->situation()->nref(scenario: $scenario, mois: $mois);
            $rdim = $this->moteur_dimensionnement->calcule_dimensionnement($entity);

            $pertes = $this->pertes_distribution_ind_vc(
                becs: $becs,
                surface: $entity->installation()->surface(),
                rdim: $rdim,
            );
            $pertes += $this->pertes_distribution_col_vc(
                becs: $becs,
                installation_collective: $entity->generateur()->signaletique()->generateur_collectif,
                rdim: $rdim,
            );
            $pertes += $this->pertes_distribution_col_hvc(
                becs: $becs,
                installation_collective: $entity->generateur()->signaletique()->generateur_collectif,
                rdim: $rdim,
            );
            return Perte::create(
                scenario: $scenario,
                mois: $mois,
                type: TypePerte::DISTRIBUTION,
                pertes: $pertes,
                pertes_recuperables: $this->pertes_distribution_recuperables(
                    pertes_distribution: $pertes,
                    nref: $nref,
                ),
            );
        });
    }

    public function calcule_pertes_generation(Generateur $entity, Simulation $simulation): PerteCollection
    {
        return PerteCollection::create(function (ScenarioUsage $scenario, Mois $mois) use ($entity, $simulation) {
            if (false === $entity->ecs()->installations()->has_generateur($entity->id())) {
                return Perte::create(
                    type: TypePerte::GENERATION,
                    scenario: $scenario,
                    mois: $mois,
                    pertes: 0,
                    pertes_recuperables: 0,
                );
            }
            $pertes = $this->pertes_generation(
                usage_generateur: $entity->usage(),
                qp0: $entity->performance()->qp0 ?? 0,
                presence_ventouse: $entity->signaletique()->presence_ventouse ?? false,
                nref: $simulation->audit()->situation()->nref(scenario: $scenario, mois: $mois),
            );
            return Perte::create(
                type: TypePerte::GENERATION,
                scenario: $scenario,
                mois: $mois,
                pertes: $pertes,
                pertes_recuperables: $this->pertes_generation_recuperables(
                    pertes_generation: $pertes,
                    position_volume_chauffe: $entity->signaletique()->position_volume_chauffe,
                ),
            );
        });
    }

    public function calcule_pertes_stockage_generateur(Generateur $entity, Simulation $simulation): PerteCollection
    {
        return PerteCollection::create(function (ScenarioUsage $scenario, Mois $mois) use ($entity, $simulation) {
            $pertes = $this->pertes_stockage(
                volume_stockage: $entity->signaletique()->volume_stockage,
                type_generateur: $entity->signaletique()->type,
                label_generateur: $entity->signaletique()?->label,
            );
            return Perte::create(
                scenario: $scenario,
                mois: $mois,
                type: TypePerte::STOCKAGE,
                pertes: $pertes,
                pertes_recuperables: $this->pertes_stockage_recuperables(
                    pertes_stockage: $pertes,
                    nref: $simulation->audit()->situation()->nref(scenario: $scenario, mois: $mois),
                    position_volume_chauffe: $entity->signaletique()->position_volume_chauffe,
                ),
            );
        });
    }

    public function calcule_pertes_stockage_systeme(Systeme $entity, Simulation $simulation): PerteCollection
    {
        return PerteCollection::create(function (ScenarioUsage $scenario, Mois $mois) use ($entity, $simulation) {
            $pertes = $this->pertes_stockage(
                volume_stockage: $entity->stockage()?->volume_stockage ?? 0,
                type_generateur: $entity->generateur()->signaletique()->type,
                label_generateur: $entity->generateur()->signaletique()?->label,
            );
            return Perte::create(
                scenario: $scenario,
                mois: $mois,
                type: TypePerte::STOCKAGE,
                pertes: $pertes,
                pertes_recuperables: $this->pertes_stockage_recuperables(
                    pertes_stockage: $pertes,
                    nref: $simulation->audit()->situation()->nref(scenario: $scenario, mois: $mois),
                    position_volume_chauffe: $entity->stockage()?->position_volume_chauffe ?? false,
                ),
            );
        });
    }

    /**
     * Pertes de distribution récupérables pour le mois en Wh
     * 
     * @param float $pertes_distribution - Pertes de distribution pour le mois en Wh
     * @param float $nref - Nombre d'heures de chauffage pour le mois en h
     */
    public function pertes_distribution_recuperables(float $pertes_distribution, float $nref): float
    {
        return 0.48 * $nref * ($pertes_distribution / 8760);
    }

    /**
     * Pertes de distribution individuelle en volume chauffé pour le mois en Wh
     * 
     * @param float $becs - Besoin d'eau chaude sanitaire pour le mois en kWh
     * @param float $surface - Surface de l'installation en m²
     * @param float $rdim - Ratio de dimensionnement (installation x systeme)
     */
    public function pertes_distribution_ind_vc(float $becs, float $surface, float $rdim,): float
    {
        $lvc = 0.2 * $surface * $rdim;
        return (0.5 * $lvc) / $surface * $becs * 1000;
    }

    /**
     * Pertes de distribution collective en volume chauffé pour le mois en Wh
     * 
     * @param float $becs - Besoin d'eau chaude sanitaire pour le mois en kWh
     * @param float $rdim - Ratio de dimensionnement (installation x systeme)
     */
    public function pertes_distribution_col_vc(float $becs, bool $installation_collective, float $rdim,): float
    {
        return $installation_collective ? 0.112 * $becs * 1000 * $rdim : 0;
    }

    /**
     * Pertes de distribution collective hors volume chauffé pour le mois en Wh
     * 
     * @param float $becs - Besoin d'eau chaude sanitaire pour le mois en kWh
     * @param float $rdim - Ratio de dimensionnement (installation x systeme)
     */
    public function pertes_distribution_col_hvc(float $becs, bool $installation_collective, float $rdim,): float
    {
        return $installation_collective ? 0.028 * $becs * 1000 * $rdim : 0;
    }

    /**
     * Pertes de génération récupérables pour le mois en Wh
     * 
     * @float $pertes_generation - Pertes de génération pour le mois en Wh
     */
    public function pertes_generation_recuperables(float $pertes_generation, bool $position_volume_chauffe,): float
    {
        return $position_volume_chauffe ? $pertes_generation : 0;
    }

    /**
     * Pertes de génération pour le mois en Wh
     * 
     * Les pertes de génération des générateurs mixtes sont traitées par le domaine Chauffage
     * 
     * @param float $qp0 - Pertes à l'arrêt du générateur en W
     * @param float $nref - Nombre d'heures de chauffage pour le mois en h
     */
    public function pertes_generation(
        UsageEcs $usage_generateur,
        float $qp0,
        bool $presence_ventouse,
        float $nref,
    ): float {
        $cper = $presence_ventouse ? 0.75 : 0.5;
        $dper = $nref * (1790 / 8760);
        return $usage_generateur === UsageEcs::ECS ? 0.48 * $cper * $qp0 * $dper : 0;
    }

    /**
     * Pertes mensuelles de stockage récupérables en Wh
     */
    public function pertes_stockage_recuperables(float $pertes_stockage, float $nref, bool $position_volume_chauffe): float
    {
        return $position_volume_chauffe ? 0.48 * $nref * ($pertes_stockage / 8760) : 0;
    }

    /**
     * Qgw - Pertes de stockage pour le mois en Wh - Mensualisation pour cohérence des données de sortie
     */
    public function pertes_stockage(
        int $volume_stockage,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
    ): float {
        if (0 === $volume_stockage)
            return 0;

        if (\in_array($type_generateur, [TypeGenerateur::CHAUFFE_EAU_HORIZONTAL, TypeGenerateur::CHAUFFE_EAU_VERTICAL])) {
            if (null === $data = $this->cr_repository->find_by(
                type_generateur: $type_generateur,
                label_generateur: $label_generateur ?? LabelGenerateur::INCONNU,
                volume_stockage: $volume_stockage,
            )) throw new \DomainException("Valeur forfaitaire Cr non trouvée");

            return (8592 * (45 / 24) * $volume_stockage * $data->cr) / 12;
        }
        return (67662 * \pow($volume_stockage, 0.55)) / 12;
    }
}
