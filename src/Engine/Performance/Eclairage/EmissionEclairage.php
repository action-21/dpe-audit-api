<?php

namespace App\Engine\Performance\Eclairage;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Eclairage\Eclairage;
use App\Engine\Performance\Rule;

final class EmissionEclairage extends Rule
{
    private Eclairage $eclairage;

    /**
     * Emissions de CO2 exprimées en kg
     * 
     * @see \App\Engine\Performance\Eclairage\ConsommationEclairage::cecl()
     */
    public function eges(ScenarioUsage $scenario): float
    {
        return $this->eclairage->data()->consommations->get($scenario) * 0.069;
    }

    public function apply(Audit $entity): void
    {
        $this->eclairage = $entity->eclairage();

        $emissions = Emissions::create(
            usage: Usage::ECLAIRAGE,
            callback: fn(ScenarioUsage $scenario) => $this->eges($scenario),
        );

        $entity->eclairage()->calcule($entity->eclairage()->data()->with(
            emissions: $emissions,
        ));
        $entity->calcule($entity->data()->with(
            emissions: $emissions,
        ));
    }

    public static function dependencies(): array
    {
        return [ConsommationEclairage::class];
    }
}
