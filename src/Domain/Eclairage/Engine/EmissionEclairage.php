<?php

namespace App\Domain\Eclairage\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Eclairage\Eclairage;

final class EmissionEclairage extends EngineRule
{
    private Eclairage $eclairage;

    /**
     * Emissions de CO2 exprimées en kg
     * 
     * @see \App\Domain\Eclairage\Engine\ConsommationEclairage::cecl()
     */
    public function eges(ScenarioUsage $scenario): float
    {
        return $this->eclairage->data()->consommations->get($scenario) * 0.069;
    }

    public function apply(Audit $entity): void
    {
        $this->eclairage = $entity->eclairage();

        $entity->eclairage()->calcule($entity->eclairage()->data()->with(
            emissions: Emissions::create(
                usage: Usage::ECLAIRAGE,
                callback: fn(ScenarioUsage $scenario) => $this->eges($scenario),
            ),
        ));
    }

    public static function dependencies(): array
    {
        return [ConsommationEclairage::class];
    }
}
