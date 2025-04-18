<?php

namespace App\Domain\Chauffage\Engine\Emission;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Chauffage\Engine\Consommation\ConsommationChauffage;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\EnergieGenerateur;

final class EmissionChauffage extends EngineRule
{
    private Systeme $systeme;

    /**
     * @see \App\Domain\Chauffage\Engine\Consommation\ConsommationChauffage::cch()
     */
    public function cch(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::CHAUFFAGE,
        );
    }

    /**
     * Emissions de CO2 exprimées en kg
     */
    public function eges(ScenarioUsage $scenario): float
    {
        if ($this->systeme->generateur()->position()->reseau_chaleur) {
            return $this->cch($scenario) * $this->systeme->generateur()->position()->reseau_chaleur->contenu_co2()->number();
        }
        return $this->cch($scenario) * match ($this->systeme->generateur()->energie()) {
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
        if (0 === $entity->chauffage()->systemes()->count()) {
            $entity->chauffage()->calcule($entity->chauffage()->data()->with(
                emissions: Emissions::from()
            ));
        }
        foreach ($entity->chauffage()->systemes() as $systeme) {
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
            $systeme->chauffage()->calcule($systeme->chauffage()->data()->with(
                emissions: $emissions,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ConsommationChauffage::class, EmissionAuxiliaire::class];
    }
}
