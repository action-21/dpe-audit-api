<?php

namespace App\Domain\Chauffage\Service;

use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\UsageChauffage;
use App\Domain\Chauffage\ValueObject\{Perte, PerteCollection};
use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Simulation\Simulation;

final class MoteurPerte
{
    public function __construct(private MoteurBesoin $moteur_besoin,) {}

    public function calcule_pertes_generation(Generateur $entity, Simulation $simulation): PerteCollection
    {
        $besoins_bruts = $this->moteur_besoin->calcule_besoins_bruts(
            entity: $entity->chauffage(),
            simulation: $simulation
        );

        return PerteCollection::create(function (ScenarioUsage $scenario, Mois $mois) use ($entity, $simulation, $besoins_bruts): Perte {
            $pertes = $entity->chauffage()->installations()->has_generateur($entity->id()) ? $this->pertes_generation(
                usage_generateur: $entity->usage(),
                bch_hp: $besoins_bruts->besoins(scenario: $scenario, mois: $mois),
                nref: $simulation->audit()->situation()->nref(scenario: $scenario, mois: $mois),
                pn: $entity->performance()->pn,
                qp0: $entity->performance()->qp0 ?? 0,
                presence_ventouse: $entity->signaletique()?->presence_ventouse ?? false,
            ) : 0;
            $pertes_recuperables = $this->pertes_generation_recuperables(
                pertes_generation: $pertes,
                position_volume_chauffe: $entity->position_volume_chauffe(),
            );
            return Perte::create(
                scenario: $scenario,
                mois: $mois,
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            );
        });
    }

    /**
     * Pertes mensuelles de génération récupérables en Wh
     * 
     * @param float $pertes_generation - Pertes de génération sur le mois en Wh
     */
    public function pertes_generation_recuperables(float $pertes_generation, bool $position_volume_chauffe): float
    {
        return $position_volume_chauffe ? $pertes_generation : 0;
    }

    /**
     * Pertes de génération sur le mois en Wh
     * 
     * @param float $bch_hp - Besoin de chauffage hors pertes récupérables sur le mois (kWh PCI)
     * @param float $nref - Nombre d'heures de chauffage sur le mois
     * @param float $pn - Puissance nominale du générateur (W)
     * @param float $qp0 - Perte à l'arrêt du générateur (W)
     */
    public function pertes_generation(
        UsageChauffage $usage_generateur,
        float $bch_hp,
        float $nref,
        float $pn,
        float $qp0,
        bool $presence_ventouse,
    ): float {
        $cper = $presence_ventouse ? 0.75 : 0.5;
        $dper = (1.3 * $bch_hp) / (0.3 * $pn);

        if ($usage_generateur === UsageChauffage::CHAUFFAGE_ECS)
            $dper += $nref * (1790 / 8760);

        return 0.48 * $cper * $qp0 * \min($nref, $dper);
    }
}
