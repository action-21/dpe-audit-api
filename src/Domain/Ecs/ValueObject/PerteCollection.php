<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Ecs\Enum\TypePerte;

/**
 * @property Perte[] $elements
 */
final class PerteCollection extends ArrayCollection
{
    public static function create(\Closure $callback): self
    {
        $collection = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            foreach (Mois::cases() as $mois) {
                $collection[] = $callback(scenario: $scenario, mois: $mois);
            }
        }
        return new self($collection);
    }

    public function filter_by_type(TypePerte $type): self
    {
        return $this->filter(fn(Perte $item) => $item->type === $type);
    }

    public function pertes(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Perte $item) => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Perte $item) => null === $mois || $item->mois === $mois ? $carry += $item->pertes : $carry);
    }

    public function pertes_recuperables(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Perte $item) => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Perte $item) => null === $mois || $item->mois === $mois ? $carry += $item->pertes_recuperables : $carry);
    }
}
