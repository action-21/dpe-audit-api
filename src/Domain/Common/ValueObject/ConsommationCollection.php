<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Energie, Mois, ScenarioUsage, Usage};

/**
 * @property Consommation[] $elements
 */
final class ConsommationCollection extends ArrayCollection
{
    public static function create(Usage $usage, Energie $energie, \Closure $callback): self
    {
        $collection = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            $collection[] = Consommation::create(
                usage: $usage,
                energie: $energie,
                scenario: $scenario,
                consommation: $callback(scenario: $scenario),
            );
        }
        return new self($collection);
    }

    public function find(ScenarioUsage $scenario): ?Consommation
    {
        foreach ($this->elements as $item) {
            if ($item->scenario === $scenario)
                return $item;
        }
        return null;
    }

    public function filter_by_usage(Usage $usage): self
    {
        return $this->filter(fn(Consommation $item) => $item->usage === $usage);
    }

    public function filter_by_scenario(ScenarioUsage $scenario): self
    {
        return $this->filter(fn(Consommation $item) => $item->scenario === $scenario);
    }

    public function filter_by_energie(Energie $energie): self
    {
        return $this->filter(fn(Consommation $item) => $item->energie === $energie);
    }

    public function consommations(?Usage $usage = null, ?ScenarioUsage $scenario = null, bool $energie_primaire = false): float
    {
        $collection = $usage ? $this->filter_by_usage($usage) : $this;
        $collection = $scenario ? $collection->filter_by_scenario($scenario) : $collection;
        return $collection->reduce(fn(float $carry, Consommation $item) => $carry += $energie_primaire ? $item->consommation_ep : $item->consommation_ef);
    }
}
