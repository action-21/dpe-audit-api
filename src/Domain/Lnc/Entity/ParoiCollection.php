<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Paroi[] $elements
 */
abstract class ParoiCollection extends ArrayCollection
{
    public function filter_by_isolation(bool $isolation): self
    {
        return $this->filter(fn(ParoiOpaque $item): bool => $item->surface_deperditive()?->isolation->boolval() === $isolation);
    }

    public function aue(?bool $isolation = null): float
    {
        $collection = $isolation === null ? $this : $this->filter_by_isolation($isolation);
        return $collection->reduce(fn(float $carry, ParoiOpaque $item): float => $carry += $item->surface_deperditive()?->aue);
    }

    public function aiu(?bool $isolation = null): float
    {
        $collection = $isolation === null ? $this : $this->filter_by_isolation($isolation);
        return $collection->reduce(fn(float $carry, ParoiOpaque $item): float => $carry += $item->surface_deperditive()?->aiu);
    }

    public function isolation_aue(): bool
    {
        return $this->aue(isolation: true) > $this->aue();
    }

    public function isolation_aiu(): bool
    {
        return $this->aiu(isolation: true) > $this->aiu();
    }
}
