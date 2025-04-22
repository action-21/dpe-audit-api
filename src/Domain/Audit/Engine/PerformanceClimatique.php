<?php

namespace App\Domain\Audit\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Chauffage\Engine\Emission\EmissionChauffage;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Eclairage\Engine\EmissionEclairage;
use App\Domain\Ecs\Engine\Emission\EmissionEcs;
use App\Domain\Refroidissement\Engine\EmissionRefroidissement;
use App\Domain\Ventilation\Engine\EmissionVentilation;

final class PerformanceClimatique extends EngineRule
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
     * Emisssions annuelles de CO2 exprimées en kg/m²
     * 
     * @see \App\Domain\Chauffage\Engine\Emission\EmissionChauffage::eges()
     * @see \App\Domain\Chauffage\Engine\Emission\EmissionAuxiliaire::eges()
     * @see \App\Domain\Ecs\Engine\Emission\EmissionEcs::eges()
     * @see \App\Domain\Ecs\Engine\Emission\EmissionAuxiliaire::eges()
     * @see \App\Domain\Refroidissement\Engine\EmissionRefroidissement::eges()
     * @see \App\Domain\Eclairage\Engine\EmissionEclairage::eges()
     * @see \App\Domain\Ventilation\Engine\EmissionVentilation::eges()
     */
    public function eges(): float
    {
        return $this->audit->data()->emissions->get() / $this->surface_habitable();
    }

    /**
     * Etiquette climat
     */
    public function etiquette_climat(): Etiquette
    {
        if (null === $etiquette = $this->table_repository->etiquette_climat(
            zone_climatique: $this->audit->adresse()->zone_climatique,
            altitude: $this->audit->batiment()->altitude,
            eges: $this->eges(),
        )) {
            throw new \DomainException("Etiquette climat non trouvée");
        }
        return $etiquette;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $entity->calcule($entity->data()->with(
            eges: $this->eges(),
            etiquette_climat: $this->etiquette_climat(),
        ));
    }

    public static function dependencies(): array
    {
        return [
            ZoneThermique::class,
            EmissionChauffage::class,
            EmissionEclairage::class,
            EmissionEcs::class,
            EmissionRefroidissement::class,
            EmissionVentilation::class,
        ];
    }
}
