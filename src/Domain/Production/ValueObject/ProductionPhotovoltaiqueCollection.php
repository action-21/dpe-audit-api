<?php

namespace App\Domain\Production\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Mois;

/**
 * @property ProductionPhotovoltaique[] $elements
 */
final class ProductionPhotovoltaiqueCollection extends ArrayCollection
{
    public function find(Mois $mois): ProductionPhotovoltaique
    {
        foreach ($this->elements as $item) {
            if ($item->mois === $mois) {
                return $item;
            }
        }
    }

    public function ppv(): float
    {
        return $this->reduce(fn(float $carry, ProductionPhotovoltaique $item) => $carry += $item->ppv);
    }
}
