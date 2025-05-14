<?php

namespace App\Engine\Performance\Chauffage\Emission;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Chauffage\Entity\Systeme;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Chauffage\Consommation\ConsommationAuxiliaire;

final class EmissionAuxiliaire extends Rule
{
    private Systeme $systeme;

    /**
     * @see \App\Engine\Performance\Chauffage\Consommation\ConsommationAuxiliaire::caux()
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
        return $this->caux($scenario) * 0.079;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->chauffage()->systemes()->count()) {
            $entity->chauffage()->calcule($entity->chauffage()->data()->with(
                emissions: Emissions::from()
            ));
        }
        foreach ($entity->chauffage()->systemes() as $systeme) {
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
            $systeme->chauffage()->calcule($systeme->chauffage()->data()->with(
                emissions: $emissions,
            ));
            $entity->calcule($entity->data()->with(
                emissions: $emissions,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ConsommationAuxiliaire::class];
    }
}
