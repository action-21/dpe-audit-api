<?php

namespace App\Domain\Batiment\Table;

use App\Domain\Common\Enum\Mois;

/**
 * @property $values Nhecl[]
 */
class NheclCollection
{
    public function __construct(public readonly array $values)
    {
    }

    public function get(Mois $mois): ?Nhecl
    {
        $collection = \array_filter($this->values, fn (Nhecl $item): bool => $item->mois === $mois);
        return \count($collection) > 0 ? \reset($collection) : null;
    }
}
