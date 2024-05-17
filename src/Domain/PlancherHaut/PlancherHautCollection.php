<?php

namespace App\Domain\PlancherHaut;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Paroi\ParoiOpaqueCollection;

/**
 * @property PlancherHaut[] $elements
 */
final class PlancherHautCollection extends ParoiOpaqueCollection
{
    public function find(Id $id): ?PlancherHaut
    {
        return parent::find($id);
    }

    /**
     * Surfaces déperditives (m²)
     */
    public function surface_deperditive(): float
    {
        return $this->reduce(fn (float $carry, PlancherHaut $item): float => $carry += $item->surface_deperditive(), 0);
    }
}
