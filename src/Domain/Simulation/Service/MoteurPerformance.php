<?php

namespace App\Domain\Simulation\Service;

use App\Domain\Common\Enum\{ScenarioUsage, Usage, ZoneClimatique};
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Simulation\Data\{EtiquetteClimatRepository, EtiquetteEnergieRepository};
use App\Domain\Simulation\Enum\Etiquette;
use App\Domain\Simulation\Simulation;
use App\Domain\Simulation\ValueObject\{Bilan, Performance, PerformanceCollection};

final class MoteurPerformance
{
    public function __construct(
        private EtiquetteEnergieRepository $etiquette_energie_repository,
        private EtiquetteClimatRepository $etiquette_climat_repository,
    ) {}

    public function calcule_performance(Simulation $entity): PerformanceCollection
    {
        $surface_reference = $entity->surface_habitable_reference();

        $consommations = ConsommationCollection::fromCollections(
            $entity->chauffage()->consommations(),
            $entity->ecs()->consommations(),
            $entity->refroidissement()->consommations(),
            $entity->ventilation()->consommations(),
        );

        return PerformanceCollection::create(function (Usage $usage, ScenarioUsage $scenario) use ($surface_reference, $consommations): Performance {
            $consommation_ef = $consommations->consommations(usage: $usage, scenario: $scenario) / $surface_reference;
            $consommation_ep = $consommations->consommations(usage: $usage, scenario: $scenario, energie_primaire: true) / $surface_reference;
            $emission = 0;

            return Performance::create(
                usage: $usage,
                scenario: $scenario,
                consommation_ef: $consommation_ef,
                consommation_ep: $consommation_ep,
                emission: $emission,
            );
        });
    }

    public function calcule_bilan(Simulation $entity): Bilan
    {
        $consommations = $entity->performances()->consommations(scenario: ScenarioUsage::CONVENTIONNEL, energie_primaire: true);
        $emissions = 0;
        $etiquette_energie = $this->etiquette_energie(
            zone_climatique: $entity->zone_climatique(),
            altitude: $entity->audit()->altitude(),
            cep: $consommations,
            eges: $emissions,
        );
        $etiquette_climat = $this->etiquette_climat(
            cep: $emissions,
        );

        return Bilan::create(
            consommation: $consommations,
            emission: $emissions,
            etiquette_energie: $etiquette_energie,
            etiquette_climat: $etiquette_climat,
        );
    }

    public function etiquette_energie(ZoneClimatique $zone_climatique, int $altitude, float $cep, float $eges,): Etiquette
    {
        if (null === $data = $this->etiquette_energie_repository->find(
            zone_climatique: $zone_climatique,
            altitude: $altitude,
            cep: $cep,
            eges: $eges,
        )) throw new \DomainException("Étiquette énergie non trouvée");

        return $data;
    }

    public function etiquette_climat(float $cep): Etiquette
    {
        if (null === $data = $this->etiquette_climat_repository->find($cep))
            throw new \DomainException("Étiquette climat non trouvée");

        return $data;
    }
}
