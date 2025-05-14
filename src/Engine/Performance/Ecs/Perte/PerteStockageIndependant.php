<?php

namespace App\Engine\Performance\Ecs\Perte;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, TypePerte, Usage};
use App\Domain\Common\ValueObject\Pertes;
use App\Domain\Ecs\Entity\Systeme;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;

final class PerteStockageIndependant extends Rule
{
    private Audit $audit;
    private Systeme $systeme;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->nref(scenario: $scenario, mois: $mois);
    }

    /**
     * Pertes mensuelles de stockage exprimées en Wh
     */
    public function pertes_stockage(): float
    {
        $vs = $this->systeme->stockage()?->volume ?? 0;
        return $vs ? (67662 * \pow($vs, 0.55)) / 12 : 0;
    }

    /**
     * Pertes mensuelles de stockage récupérables exprimées en Wh
     */
    public function pertes_stockage_recuperables(ScenarioUsage $scenario, Mois $mois): float
    {
        if (!$this->systeme->stockage()?->position_volume_chauffe) {
            return 0;
        }
        $nref = $this->nref(scenario: $scenario, mois: $mois);
        return 0.48 * $nref * ($this->pertes_stockage() / 8760);
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $pertes = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::STOCKAGE,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_stockage(),
            );
            $pertes_recuperables = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::STOCKAGE,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_stockage_recuperables(
                    scenario: $scenario,
                    mois: $mois,
                ),
            );

            $systeme->calcule($systeme->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
            $systeme->ecs()->calcule($systeme->ecs()->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ScenarioClimatique::class];
    }
}
