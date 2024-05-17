<?php

namespace App\Domain\PlancherIntermediaire;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property PlancherIntermediaire[] $elements
 */
final class PlancherIntermediaireCollection extends ArrayCollection
{
    public function find(Id $id): ?PlancherIntermediaire
    {
        return $this->findFirst(fn (mixed $key, PlancherIntermediaire $item): bool => $item->id()->compare($id));
    }

    public function surface(): float
    {
        return $this->reduce(fn (float $carry, PlancherIntermediaire $item): float => $carry += $item->dimensions()->surface->valeur(), 0);
    }

    /**
     * Surface déductible des surfaces déperditives en m²
     */
    public function emprise(): float
    {
        return $this->reduce(fn (float $carry, PlancherIntermediaire $item): float => $carry += $item->dimensions()->emprise(), 0);
    }

    /**
     * Filtre par plancher haut lourd
     */
    public function search_by_plancher_haut_lourd(): self
    {
        return $this->filter(fn (PlancherIntermediaire $item) => $item->plancher_haut_lourd());
    }

    /**
     * Filtre par plancher bas lourd
     */
    public function search_by_plancher_bas_lourd(): self
    {
        return $this->filter(fn (PlancherIntermediaire $item) => $item->plancher_bas_lourd());
    }
}
