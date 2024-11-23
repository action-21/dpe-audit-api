<?php

namespace App\Domain\Baie\Data;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Mois;

/**
 * @property C1[] $elements
 */
final class C1Collection extends ArrayCollection
{
    public function find(Mois $mois): ?C1
    {
        return $this->findFirst(fn(mixed $key, C1 $item): bool => $item->mois === $mois);
    }

    public function est_valide(): bool
    {
        foreach (Mois::cases() as $mois)
            if (null === $this->find($mois)) return false;

        return true;
    }
}
