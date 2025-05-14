<?php

namespace App\Engine\Performance\Ventilation;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Emissions;
use App\Domain\Ventilation\Entity\Systeme;
use App\Engine\Performance\Rule;

final class EmissionVentilation extends Rule
{
    private Systeme $systeme;

    /**
     * Emissions de CO2 exprimées en kg
     * 
     * @see \App\Engine\Performance\Ventilation\ConsommationVentilation::caux()
     * 
     * @see https://www.legifrance.gouv.fr/loda/article_lc/LEGIARTI000046662777
     */
    public function eges(ScenarioUsage $scenario): float
    {
        if (null === $this->systeme->generateur()) {
            return 0;
        }
        return $this->systeme->generateur()->data()->consommations->get($scenario) * 0.064;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->ventilation()->systemes()->count()) {
            $entity->ventilation()->calcule($entity->ventilation()->data()->with(
                emissions: Emissions::from()
            ));
        }
        foreach ($entity->ventilation()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $emissions = Emissions::create(
                usage: Usage::AUXILIAIRE,
                callback: fn(ScenarioUsage $scenario) => $this->eges($scenario),
            );

            $systeme->calcule($systeme->data()->with(
                emissions: $emissions,
            ));
            $systeme->generateur()?->calcule($systeme->generateur()->data()->with(
                emissions: $emissions,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                emissions: $emissions,
            ));
            $systeme->ventilation()->calcule($systeme->ventilation()->data()->with(
                emissions: $emissions,
            ));
            $entity->calcule($entity->data()->with(
                emissions: $emissions,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ConsommationVentilation::class];
    }
}
