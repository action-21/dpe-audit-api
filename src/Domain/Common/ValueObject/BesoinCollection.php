<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};

/**
 * @property Besoin[] $elements
 */
final class BesoinCollection extends ArrayCollection
{
    public static function create(Usage $usage, \Closure $callback): self
    {
        $collection = new self();
        foreach (ScenarioUsage::cases() as $scenario) {
            foreach (Mois::cases() as $mois) {
                $collection->add(Besoin::create(
                    usage: $usage,
                    scenario: $scenario,
                    mois: $mois,
                    besoin: $callback(scenario: $scenario, mois: $mois),
                ));
            }
        }
        return $collection;
    }

    public function find(ScenarioUsage $scenario, Mois $mois): ?Besoin
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
        return $this->filter(fn(Besoin $item) => $item->usage === $usage);
    }

    public function filter_by_scenario(ScenarioUsage $scenario): self
    {
        return $this->filter(fn(Besoin $item) => $item->scenario === $scenario);
    }

    public function besoins(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Besoin $item) => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Besoin $item) => null === $mois || $item->mois === $mois ? $carry += $item->besoin : $carry);
    }
}
