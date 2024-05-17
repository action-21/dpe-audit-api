<?php

namespace App\Domain\PlancherBas;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Paroi\ParoiOpaqueCollection;

/**
 * @property PlancherBas[] $elements
 */
final class PlancherBasCollection extends ParoiOpaqueCollection
{
    public function find(Id $id): ?PlancherBas
    {
        return parent::find($id);
    }

    /**
     * Surfaces déperditives (m²)
     */
    public function surface_deperditive(): float
    {
        return $this->reduce(fn (float $carry, PlancherBas $item): float => $carry += $item->surface_deperditive(), 0);
    }
}
