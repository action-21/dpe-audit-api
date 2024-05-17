<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Common\Table\TableValueCollection;
use App\Domain\Common\Enum\Orientation;

/**
 * @property Bver[] $elements
 */
class BVerCollection extends TableValueCollection
{
    /**
     * @param Orientation[] $orientation_collection
     */
    public function bver(array $orientation_collection): null|float
    {
        if (0 === $this->count() || empty($orientation_collection)) {
            return null;
        }
        $orientation_collection = \array_map(fn (Orientation $item): int => $item->value, $orientation_collection);
        $collection = $this->filter(fn (Bver $item): bool => \count(\array_intersect($orientation_collection, [$item->orientation()])) > 0);
        return $collection->reduce(fn (float $carry, Bver $item): float => $carry += $item->valeur(), 0) / $this->count();
    }
}
