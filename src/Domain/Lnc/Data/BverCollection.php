<?php

namespace App\Domain\Lnc\Data;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Orientation;

/**
 * @property Bver[] $elements
 */
final class BverCollection extends ArrayCollection
{
    private function filter_by_isolation_paroi(bool $isolation_paroi): self
    {
        return $this->filter(fn(Bver $item): bool => $item->isolation_paroi === $isolation_paroi);
    }

    /**
     * @param Orientation[] $orientations - Orientations principales des baies de l'espace tampon solarisé
     */
    public function filter_by_orientations(array $orientations): self
    {
        return $this->filter(fn(Bver $item): bool => \in_array($item->orientation, $orientations, true));
    }

    private function moyenne(): float
    {
        return $this->reduce(fn(float $carry, Bver $item): float => $carry += $item->bver, 0) / $this->count() ?? 1;
    }

    public function bver(bool $isolation_paroi): ?float
    {
        return $this->count() > 0 ? $this->filter_by_isolation_paroi($isolation_paroi)->moyenne() : null;
    }
}
