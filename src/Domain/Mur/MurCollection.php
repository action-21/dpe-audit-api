<?php

namespace App\Domain\Mur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Paroi\ParoiOpaqueCollection;

/**
 * @property Mur[] $elements
 */
final class MurCollection extends ParoiOpaqueCollection
{
    public function find(Id $id): ?Mur
    {
        return $this->findFirst(fn (mixed $key, Mur $item): bool => $item->id()->compare($id));
    }

    /**
     * Surfaces déperditives (m²)
     */
    public function surface_deperditive(): float
    {
        return $this->reduce(fn (float $carry, Mur $item): float => $carry += $item->surface_deperditive(), 0);
    }

    /**
     * @see §18.3
     * 
     * En présence de plusieurs types de parois, le bâtiment sera considéré comme constitué
     * de parois anciennes si la surface de parois anciennes est majoritaire.
     */
    public function parois_anciennes(): bool
    {
        return $this->filter(fn (Mur $item): bool => $item->caracteristique()->paroi_ancienne)->surface_deperditive() > ($this->surface_deperditive() / 2);
    }
}
