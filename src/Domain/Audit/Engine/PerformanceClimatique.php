<?php

namespace App\Domain\Audit\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Audit\ValueObject\{Emission, Emissions};
use App\Domain\Chauffage\Engine\Emission\EmissionChauffage;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
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
     * @see \App\Domain\Chauffage\Engine\Emission\EmissionChauffage::eges()
     */
    public function eges_ch(ScenarioUsage $scenario): float
    {
        return $this->audit->chauffage()->data()->emissions->get(
            scenario: $scenario,
            usage: Usage::CHAUFFAGE,
        );
    }

    /**
     * @see \App\Domain\Ecs\Engine\Emission\EmissionEcs::eges()
     */
    public function eges_ecs(ScenarioUsage $scenario): float
    {
        return $this->audit->ecs()->data()->emissions->get(
            scenario: $scenario,
            usage: Usage::ECS,
        );
    }

    /**
     * @see \App\Domain\Refroidissement\Engine\EmissionRefroidissement::eges()
     */
    public function eges_fr(ScenarioUsage $scenario): float
    {
        return $this->audit->refroidissement()->data()->emissions->get(
            scenario: $scenario,
            usage: Usage::REFROIDISSEMENT,
        );
    }

    /**
     * @see \App\Domain\Eclairage\Engine\EmissionEclairage::eges()
     */
    public function eges_ecl(ScenarioUsage $scenario): float
    {
        return $this->audit->eclairage()->data()->emissions->get(
            scenario: $scenario,
            usage: Usage::ECLAIRAGE,
        );
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Emission\EmissionAuxiliaire::eges()
     * @see \App\Domain\Ecs\Engine\Emission\EmissionAuxiliaire::eges()
     * @see \App\Domain\Ventilation\Engine\EmissionVentilation::eges()
     */
    public function eges_aux(ScenarioUsage $scenario): float
    {
        $caux = $this->audit->chauffage()->data()->emissions->get(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE,
        );
        $caux += $this->audit->ecs()->data()->emissions->get(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE,
        );
        $caux += $this->audit->ventilation()->data()->emissions->get(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE,
        );
        return $caux;
    }

    /**
     * Emisssions de CO2 exprimées en kg
     */
    public function eges(ScenarioUsage $scenario): float
    {
        return array_sum([
            $this->eges_ch($scenario),
            $this->eges_ecs($scenario),
            $this->eges_fr($scenario),
            $this->eges_ecl($scenario),
            $this->eges_aux($scenario),
        ]) / $this->surface_habitable();
    }

    /**
     * Etiquette climat
     */
    public function etiquette_climat(): Etiquette
    {
        if (null === $etiquette = $this->table_repository->etiquette_climat(
            zone_climatique: $this->audit->adresse()->zone_climatique,
            altitude: $this->audit->batiment()->altitude,
            eges: $this->eges(ScenarioUsage::CONVENTIONNEL),
        )) {
            throw new \DomainException("Etiquette climat non trouvée");
        }
        return $etiquette;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $entity->calcule($entity->data()->with(
            emissions: Emissions::create(fn(ScenarioUsage $scenario) => $this->eges($scenario)),
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
