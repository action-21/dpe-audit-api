<?php

namespace App\Domain\Audit\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Chauffage\Engine\Consommation\ConsommationChauffage;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Eclairage\Engine\ConsommationEclairage;
use App\Domain\Ecs\Engine\Consommation\ConsommationEcs;
use App\Domain\Refroidissement\Engine\ConsommationRefroidissement;
use App\Domain\Ventilation\Engine\ConsommationVentilation;

final class PerformanceEnergetique extends EngineRule
{
    private Audit $audit;

    public function __construct(private readonly AuditTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Domain\Audit\Engine\PerformanceClimatique::eges()
     */
    public function eges(): float
    {
        return $this->audit->data()->eges;
    }

    /**
     * Consommation annuelle d'énergie finale exprimée en kWh/m²
     * 
     * @see \App\Domain\Chauffage\Engine\Consommation\ConsommationChauffage::cch()
     * @see \App\Domain\Chauffage\Engine\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationEcs::cecs()
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Domain\Ventilation\Engine\ConsommationVentilation::caux()
     * @see \App\Domain\Refroidissement\Engine\ConsommationRefroidissement::cfr()
     * @see \App\Domain\Eclairage\Engine\ConsommationEclairage::cecl()
     */
    public function cef(): float
    {
        return $this->audit->data()->consommations->get(energie_primaire: false) / $this->surface_habitable();
    }

    /**
     * Consommation annuelle d'énergie primaire exprimée en kWh/m²
     * 
     * @see \App\Domain\Chauffage\Engine\Consommation\ConsommationChauffage::cch()
     * @see \App\Domain\Chauffage\Engine\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationEcs::cecs()
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Domain\Ventilation\Engine\ConsommationVentilation::caux()
     * @see \App\Domain\Refroidissement\Engine\ConsommationRefroidissement::cfr()
     * @see \App\Domain\Eclairage\Engine\ConsommationEclairage::cecl()
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
            zone_climatique: $this->audit->adresse()->zone_climatique,
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
            ConsommationChauffage::class,
            ConsommationEclairage::class,
            ConsommationEcs::class,
            ConsommationRefroidissement::class,
            ConsommationVentilation::class,
        ];
    }
}
