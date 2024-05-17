<?php

namespace App\Domain\Batiment\Table;

use App\Domain\Common\Enum\Mois;

/**
 * @property SollicitationExterieure[] $elements
 */
class SollicitationExterieureCollection
{
    public function __construct(public readonly array $values)
    {
    }

    public function get(Mois $mois): ?SollicitationExterieure
    {
        $collection = \array_filter($this->values, fn (SollicitationExterieure $item): bool => $item->mois == $mois);
        return \count($collection) > 0 ? \reset($collection) : null;
    }
}
