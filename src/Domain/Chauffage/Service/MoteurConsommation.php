<?php

namespace App\Domain\Chauffage\Service;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Common\Enum\{ScenarioUsage, Usage, ZoneClimatique};
use App\Domain\Common\ValueObject\{Consommation, ConsommationCollection};
use App\Domain\Simulation\Simulation;

final class MoteurConsommation
{
    public function __construct(
        private MoteurConsommation\MoteurConsommationAuxiliaire $moteur_consommation_auxiliaire,
        private MoteurDimensionnement $moteur_dimensionnement,
    ) {}

    public function calcule_consommations(Systeme $entity, Simulation $simulation): ConsommationCollection
    {
        $collection = new ConsommationCollection();
        foreach (ScenarioUsage::cases() as $scenario) {
            $besoins = $entity->chauffage()->besoins()->besoins(scenario: $scenario);
            $cch = $this->cch(
                bch: $besoins,
                fch: $entity->rendements()->fch(scenario: $scenario),
                int: $entity->rendements()->int(scenario: $scenario),
                ich: $entity->rendements()->ich(scenario: $scenario),
                rdim: $entity->rdim() * $entity->installation()->rdim(),
            );

            $cch *= $this->moteur_dimensionnement->calcule_taux_bch(
                entity: $entity,
                simulation: $simulation,
                scenario: $scenario,
            );

            if ($entity->generateur()->signaletique()->energie_partie_chaudiere) {
                $collection->add(Consommation::create(
                    usage: Usage::CHAUFFAGE,
                    energie: $entity->generateur()->energie()->to(),
                    scenario: $scenario,
                    consommation: $this->cch_partie_pac(
                        cch: $cch,
                        zone_climatique: $entity->chauffage()->audit()->zone_climatique(),
                    ),
                ));
                $collection->add(Consommation::create(
                    usage: Usage::CHAUFFAGE,
                    energie: $entity->generateur()->signaletique()->energie_partie_chaudiere->to(),
                    scenario: $scenario,
                    consommation: $this->cch_partie_chaudiere(
                        cch: $cch,
                        zone_climatique: $entity->chauffage()->audit()->zone_climatique(),
                    ),
                ));
            } else {
                $collection->add(Consommation::create(
                    usage: Usage::CHAUFFAGE,
                    energie: $entity->generateur()->energie()->to(),
                    scenario: $scenario,
                    consommation: $cch,
                ));
            }
        }
        return $collection;
    }

    public function calcule_consommations_auxiliaires(Systeme $entity): ConsommationCollection
    {
        return $this->moteur_consommation_auxiliaire->calcule_consommation_auxiliaire_generation($entity);
    }

    /**
     * Consommation de chauffage en kWh PCI
     * 
     * @param float $bch Besoin annuel de chauffage en kWh PCI
     * @param float $fch Facteur de couverture solaire
     * @param float $int Coefficient d'intermittence
     * @param float $ich Inverse du rendement de chauffage
     * @param float $rdim Ratio de dimensionnement (installation x système x taux de couverture hybride)
     */
    public function cch(float $bch, float $fch, float $int, float $ich, float $rdim): float
    {
        return $bch * (1 - $fch) * $int * $ich * $rdim;
    }

    /**
     * Consommation de chauffage du système hybride en kWh PCI - Partie PAC
     * 
     * @param float $cch - Consommation de chauffage en kWh PCI
     */
    public function cch_partie_pac(float $cch, ZoneClimatique $zone_climatique): float
    {
        return match ($zone_climatique->code()) {
            'H1' => $cch * 0.8,
            'H2' => $cch * 0.83,
            'H3' => $cch * 0.88,
        };
    }

    /**
     * Consommation de chauffage du système hybride en kWh PCI - Partie Chaudière
     * 
     * @param float $cch - Consommation de chauffage en kWh PCI
     */
    public function cch_partie_chaudiere(float $cch, ZoneClimatique $zone_climatique): float
    {
        return match ($zone_climatique->code()) {
            'H1' => $cch * 0.2,
            'H2' => $cch * 0.17,
            'H3' => $cch * 0.12,
        };
    }
}
