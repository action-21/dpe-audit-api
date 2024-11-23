<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};

/**
 * @property Emission[] $elements
 */
final class EmissionCollection extends ArrayCollection
{
    public static function create(Usage $usage, \Closure $callback): self
    {
        $collection = new self();
        foreach (ScenarioUsage::cases() as $scenario) {
            foreach (Mois::cases() as $mois) {
                $collection->add(Emission::create(
                    usage: $usage,
                    scenario: $scenario,
                    mois: $mois,
                    emission: $callback(scenario: $scenario, mois: $mois),
                ));
            }
        }
        return $collection;
    }

    public function find(ScenarioUsage $scenario, Mois $mois): ?Emission
    {
        foreach ($this->elements as $item) {
            if ($item->mois === $mois && $item->scenario === $scenario) {
                return $item;
            }
        }
        return null;
    }

    public function filter_by_usage(Usage $usage): self
    {
        return $this->filter(fn(Emission $item) => $item->usage === $usage);
    }

    public function filter_by_scenario(ScenarioUsage $scenario): self
    {
        return $this->filter(fn(Emission $item) => $item->scenario === $scenario);
    }

    public function emission(): float
    {
        return $this->reduce(fn(float $carry, Emission $item) => $carry += $item->emission);
    }
}
