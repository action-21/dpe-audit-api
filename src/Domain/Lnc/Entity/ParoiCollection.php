<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Paroi[] $elements
 */
class ParoiCollection extends ArrayCollection
{
    /**
     * Somme des surfaces des parois
     */
    public function surface(): float
    {
        return $this->reduce(fn (float $carry, Paroi $item): float => $carry += $item->surface()->valeur(), 0);
    }

    /**
     * État d'isolation majoritaire des parois
     * 
     * @return true si la part des parois isolées est strictement supérieure à 50%
     * @return false sinon
     */
    public function isolation(): bool
    {
        return $this->filter(fn (Paroi $item): bool => $item->isolation())->surface() > ($this->surface() / 2);
    }
}
