<?php

namespace App\Domain\Eclairage\Data;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Mois;

/**
 * @property Nhj[] $elements
 */
final class NhjCollection extends ArrayCollection
{
    public function est_valide(): bool
    {
        foreach (Mois::cases() as $mois)
            if (null === $this->findFirst(fn(mixed $key, Nhj $item): bool => $item->mois === $mois))
                return false;

        return true;
    }

    public function nhj(Mois $mois): float
    {
        return $this
            ->filter(fn(Nhj $item): bool => $item->mois === $mois)
            ->reduce(fn(float $carry, Nhj $item): float => $carry += $item->nhj);
    }
}
