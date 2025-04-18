<?php

namespace App\Domain\Ecs\Engine\Emission;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Ecs\Engine\Consommation\ConsommationAuxiliaire;
use App\Domain\Ecs\Entity\Systeme;

final class EmissionAuxiliaire extends EngineRule
{
    private Systeme $systeme;

    /**
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationAuxiliaire::caux()
     */
    public function caux(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE
        );
    }

    /**
     * Emissions de CO2 exprimées en kg
     */
    public function eges(ScenarioUsage $scenario): float
    {
        return $this->caux($scenario) * 0.064;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->ecs()->systemes()->count()) {
            $entity->ecs()->calcule($entity->ecs()->data()->with(
                emissions: Emissions::from()
            ));
        }
        foreach ($entity->ecs()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $emissions = Emissions::create(
                usage: Usage::AUXILIAIRE,
                callback: fn(ScenarioUsage $scenario) => $this->eges($scenario),
            );

            $systeme->calcule($systeme->data()->with(
                emissions: $emissions,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                emissions: $emissions,
            ));
            $systeme->generateur()->calcule($systeme->generateur()->data()->with(
                emissions: $emissions,
            ));
            $systeme->ecs()->calcule($systeme->ecs()->data()->with(
                emissions: $emissions,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ConsommationAuxiliaire::class];
    }
}
