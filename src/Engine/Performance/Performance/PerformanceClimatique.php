<?php

namespace App\Engine\Performance\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Engine\Performance\Chauffage\Emission\EmissionChauffage;
use App\Engine\Performance\Eclairage\EmissionEclairage;
use App\Engine\Performance\Ecs\Emission\EmissionEcs;
use App\Engine\Performance\Refroidissement\EmissionRefroidissement;
use App\Engine\Performance\Ventilation\EmissionVentilation;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\{ScenarioClimatique, ZoneThermique};

final class PerformanceClimatique extends Rule
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
     * Emisssions annuelles de CO2 exprimées en kg/m²
     * 
     * @see \App\Engine\Performance\Chauffage\Emission\EmissionChauffage::eges()
     * @see \App\Engine\Performance\Chauffage\Emission\EmissionAuxiliaire::eges()
     * @see \App\Engine\Performance\Ecs\Emission\EmissionEcs::eges()
     * @see \App\Engine\Performance\Ecs\Emission\EmissionAuxiliaire::eges()
     * @see \App\Engine\Performance\Refroidissement\EmissionRefroidissement::eges()
     * @see \App\Engine\Performance\Eclairage\EmissionEclairage::eges()
     * @see \App\Engine\Performance\Ventilation\EmissionVentilation::eges()
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
            zone_climatique: $this->zone_climatique(),
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
            ScenarioClimatique::class,
            EmissionChauffage::class,
            EmissionEclairage::class,
            EmissionEcs::class,
            EmissionRefroidissement::class,
            EmissionVentilation::class,
        ];
    }
}
