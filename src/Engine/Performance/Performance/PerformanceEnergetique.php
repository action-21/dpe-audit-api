<?php

namespace App\Engine\Performance\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Engine\Performance\Chauffage\Consommation\ConsommationChauffage;
use App\Engine\Performance\Eclairage\ConsommationEclairage;
use App\Engine\Performance\Ecs\Consommation\ConsommationEcs;
use App\Engine\Performance\Refroidissement\ConsommationRefroidissement;
use App\Engine\Performance\Ventilation\ConsommationVentilation;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\{ScenarioClimatique, ZoneThermique};

final class PerformanceEnergetique extends Rule
{
    private Audit $audit;

    public function __construct(private readonly AuditTableValeurRepository $table_repository) {}

    /**
     * @see \App\Engine\Performance\Scenario\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

    /**
     * @see \App\Engine\Performance\Performance\PerformanceClimatique::eges()
     */
    public function eges(): float
    {
        return $this->audit->data()->eges;
    }

    /**
     * Consommation annuelle d'énergie finale exprimée en kWh/m²
     * 
     * @see \App\Engine\Performance\Chauffage\Consommation\ConsommationChauffage::cch()
     * @see \App\Engine\Performance\Chauffage\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Engine\Performance\Ecs\Consommation\ConsommationEcs::cecs()
     * @see \App\Engine\Performance\Ecs\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Engine\Performance\Ventilation\ConsommationVentilation::caux()
     * @see \App\Engine\Performance\Refroidissement\ConsommationRefroidissement::cfr()
     * @see \App\Engine\Performance\Eclairage\ConsommationEclairage::cecl()
     */
    public function cef(): float
    {
        return $this->audit->data()->consommations->get(energie_primaire: false) / $this->surface_habitable();
    }

    /**
     * Consommation annuelle d'énergie primaire exprimée en kWh/m²
     * 
     * @see \App\Engine\Performance\Chauffage\Consommation\ConsommationChauffage::cch()
     * @see \App\Engine\Performance\Chauffage\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Engine\Performance\Ecs\Consommation\ConsommationEcs::cecs()
     * @see \App\Engine\Performance\Ecs\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Engine\Performance\Ventilation\ConsommationVentilation::caux()
     * @see \App\Engine\Performance\Refroidissement\ConsommationRefroidissement::cfr()
     * @see \App\Engine\Performance\Eclairage\ConsommationEclairage::cecl()
     */
    public function cep(): float
    {
        return $this->audit->data()->consommations->get(energie_primaire: true) / $this->surface_habitable();
    }

    /**
     * Etiquette énergie
     */
    public function etiquette_energie(): Etiquette
    {
        if (null === $etiquette = $this->table_repository->etiquette_energie(
            zone_climatique: $this->zone_climatique(),
            altitude: $this->audit->batiment()->altitude,
            cep: $this->cep(),
            eges: $this->eges(),
        )) {
            throw new \DomainException("Etiquette énergie non trouvée");
        }
        return $etiquette;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $entity->calcule($entity->data()->with(
            cef: $this->cef(),
            cep: $this->cep(),
            etiquette_energie: $this->etiquette_energie(),
        ));
    }

    public static function dependencies(): array
    {
        return [
            PerformanceClimatique::class,
            ZoneThermique::class,
            ScenarioClimatique::class,
            ConsommationChauffage::class,
            ConsommationEclairage::class,
            ConsommationEcs::class,
            ConsommationRefroidissement::class,
            ConsommationVentilation::class,
        ];
    }
}
