<?php

namespace App\Domain\Porte;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Paroi\OuvertureCollection;

/**
 * @property Porte[] $elements
 */
final class PorteCollection extends OuvertureCollection
{
    public function find(Id $id): ?Porte
    {
        return $this->findFirst(fn (mixed $key, Porte $item): bool => $item->id()->compare($id));
    }

    /**
     * Surfaces déperditives (m²)
     */
    public function surface_deperditive(): float
    {
        return $this->reduce(fn (float $carry, Porte $item): float => $carry += $item->surface_deperditive(), 0);
    }
}
