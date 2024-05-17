<?php

namespace App\Domain\Baie;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Baie[] $elements
 */
final class BaieCollection extends ArrayCollection
{
    public function find(Id $id): ?Baie
    {
        return $this->findFirst(fn (mixed $key, Baie $item): bool => $item->id()->compare($id));
    }

    /**
     * sdep,baies - Somme des surfaces déperditives des baies (m²)
     */
    public function surface_deperditive(): float
    {
        return $this->reduce(fn (float $carry, Baie $item): float => $carry += $item->surface_deperditive(), 0);
    }
}
