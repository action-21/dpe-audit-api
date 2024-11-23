<?php

namespace App\Domain\Ecs\Service;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\BesoinCollection;
use App\Domain\Ecs\Ecs;
use App\Domain\Simulation\Simulation;

/**
 * @use \App\Domain\Audit\Occupation
 * @use \App\Domain\Audit\Situation
 * @use \App\Domain\Ecs\Service\MoteurPerte
 */
final class MoteurBesoin
{
    public function calcule_besoins(Ecs $entity, Simulation $simulation): BesoinCollection
    {
        $nadeq = $simulation->audit()->occupation()->nadeq;

        return BesoinCollection::create(
            usage: Usage::ECS,
            callback: fn(ScenarioUsage $scenario, Mois $mois): float => $this->becs(
                scenario: $scenario,
                mois: $mois,
                nadeq: $nadeq,
                tefs: $simulation->audit()->situation()->tefs(mois: $mois),
            )
        );
    }

    /**
     * Besoin mensuel d'eau chaude sanitaire en kWh
     */
    public function becs(ScenarioUsage $scenario, Mois $mois, float $nadeq, float $tefs): float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => 1.163 * $nadeq * 56 * (40 - $tefs) * $mois->nj() / 1000,
            ScenarioUsage::DEPENSIER => 1.163 * $nadeq * 79 * (40 - $tefs) * $mois->nj() / 1000,
        };
    }
}
