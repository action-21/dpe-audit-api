<?php

namespace App\Domain\Simulation\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};

/**
 * @property Performance[] $elements
 */
final class PerformanceCollection extends ArrayCollection
{
    public static function create(\Closure $callback): self
    {
        $collection = [];
        foreach (Usage::usages() as $usage) {
            foreach (ScenarioUsage::cases() as $scenario) {
                $collection[] = $callback(usage: $usage, scenario: $scenario);
            }
        }
        return new self($collection);
    }

    public function consommations(ScenarioUsage $scenario, bool $energie_primaire = false): float
    {
        return $this
            ->filter(fn(Performance $item) => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Performance $item) => $carry += $energie_primaire ? $item->consommation_ep : $item->consommation_ef);
    }
}
