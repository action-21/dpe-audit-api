<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois, ScenarioUsage};

/**
 * @property Apport[] $elements
 */
final class ApportCollection extends ArrayCollection
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

    public function f(ScenarioUsage $scenario, Mois $mois): float
    {
        foreach ($this->elements as $item) {
            if ($item->scenario === $scenario && $item->mois === $mois)
                return $item->f;
        }
        return 0;
    }

    public function apports(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Apport $item): bool => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Apport $item) => null === $mois || $mois === $item->mois ? $carry += $item->apport : $carry);
    }

    public function apports_solaires(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Apport $item): bool => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Apport $item) => null === $mois || $mois === $item->mois ? $carry += $item->apport_solaire : $carry);
    }

    public function apports_internes(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Apport $item): bool => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Apport $item) => null === $mois || $mois === $item->mois ? $carry += $item->apport_interne : $carry);
    }

    public function apports_fr(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Apport $item): bool => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Apport $item) => null === $mois || $mois === $item->mois ? $carry += $item->apport_fr : $carry);
    }

    public function apports_solaires_fr(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Apport $item): bool => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Apport $item) => null === $mois || $mois === $item->mois ? $carry += $item->apport_solaire_fr : $carry);
    }

    public function apports_internes_fr(ScenarioUsage $scenario, ?Mois $mois = null): float
    {
        return $this
            ->filter(fn(Apport $item): bool => $item->scenario === $scenario)
            ->reduce(fn(float $carry, Apport $item) => null === $mois || $mois === $item->mois ? $carry += $item->apport_interne_fr : $carry);
    }
}
