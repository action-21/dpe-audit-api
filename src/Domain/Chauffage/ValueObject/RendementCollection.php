<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\ScenarioUsage;

/**
 * @property Rendement[] $elements
 */
final class RendementCollection extends ArrayCollection
{
    public static function create(\Closure $callback): self
    {
        $collection = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            $collection[] = $callback(scenario: $scenario);
        }
        return new self($collection);
    }

    public function find(ScenarioUsage $scenario): ?Rendement
    {
        foreach ($this->elements as $item) {
            if ($item->scenario === $scenario) {
                return $item;
            }
        }
        return null;
    }

    public function fch(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->fch;
    }

    public function i0(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->i0;
    }

    public function int(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->int;
    }

    public function ich(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->ich;
    }

    public function rg(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->rg;
    }

    public function rd(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->rd;
    }

    public function re(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->re;
    }

    public function rr(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->rr;
    }
}
