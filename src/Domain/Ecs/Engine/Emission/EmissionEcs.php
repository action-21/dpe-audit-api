<?php

namespace App\Domain\Ecs\Engine\Emission;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Ecs\Engine\Consommation\ConsommationEcs;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\EnergieGenerateur;

final class EmissionEcs extends EngineRule
{
    private Systeme $systeme;

    /**
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationEcs::cecs()
     */
    public function cecs(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::ECS,
        );
    }

    /**
     * Emissions de CO2 exprimées en kg
     */
    public function eges(ScenarioUsage $scenario): float
    {
        if ($this->systeme->generateur()->position()->reseau_chaleur) {
            return $this->cecs($scenario) * $this->systeme->generateur()->position()->reseau_chaleur->contenu_co2()->number();
        }
        return $this->cecs($scenario) * match ($this->systeme->generateur()->energie()) {
            EnergieGenerateur::ELECTRICITE => 0.065,
            EnergieGenerateur::GAZ_NATUREL => 0.227,
            EnergieGenerateur::GPL => 0.272,
            EnergieGenerateur::FIOUL => 0.324,
            EnergieGenerateur::BOIS_BUCHE => 0.03,
            EnergieGenerateur::BOIS_PLAQUETTE => 0.024,
            EnergieGenerateur::BOIS_GRANULE => 0.03,
            EnergieGenerateur::CHARBON => 0.385,
            EnergieGenerateur::RESEAU_CHALEUR => 0.385,
        };
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
                usage: Usage::ECS,
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
            $entity->calcule($entity->data()->with(
                emissions: $emissions,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ConsommationEcs::class, EmissionAuxiliaire::class];
    }
}
