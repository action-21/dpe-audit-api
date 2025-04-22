<?php

namespace App\Domain\Refroidissement\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Refroidissement\Entity\Systeme;
use App\Domain\Refroidissement\Enum\EnergieGenerateur;

final class EmissionRefroidissement extends EngineRule
{
    private Systeme $systeme;

    /**
     * @see \App\Domain\Refroidissement\Engine\ConsommationRefroidissement::cfr()
     */
    public function cfr(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->consommations->get($scenario);;
    }

    /**
     * Emissions de CO2 exprimées en kg
     * 
     * @see https://www.legifrance.gouv.fr/loda/article_lc/LEGIARTI000046662777
     */
    public function eges(ScenarioUsage $scenario): float
    {
        if ($this->systeme->generateur()->reseau_froid()) {
            return $this->cfr($scenario) * $this->systeme->generateur()->reseau_froid()->contenu_co2()->decimal();
        }
        return $this->cfr($scenario) * match ($this->systeme->generateur()->energie()) {
            EnergieGenerateur::ELECTRICITE => 0.064,
            EnergieGenerateur::GAZ_NATUREL => 0.227,
            EnergieGenerateur::GPL => 0.272,
            EnergieGenerateur::RESEAU_FROID => 0.385,
        };
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->refroidissement()->systemes()->count()) {
            $entity->refroidissement()->calcule($entity->refroidissement()->data()->with(
                emissions: Emissions::from()
            ));
        }
        foreach ($entity->refroidissement()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $emissions = Emissions::create(
                usage: Usage::REFROIDISSEMENT,
                callback: fn(ScenarioUsage $scenario) => $this->eges($scenario),
            );

            $systeme->calcule($systeme->data()->with(
                emissions: $emissions
            ));
            $systeme->generateur()->calcule($systeme->generateur()->data()->with(
                emissions: $emissions
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                emissions: $emissions
            ));
            $systeme->refroidissement()->calcule($systeme->refroidissement()->data()->with(
                emissions: $emissions
            ));
            $entity->calcule($entity->data()->with(
                emissions: $emissions,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ConsommationRefroidissement::class];
    }
}
