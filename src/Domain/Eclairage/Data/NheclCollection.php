<?php

namespace App\Domain\Eclairage\Data;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Mois;

/**
 * @property Nhecl[] $elements
 */
final class NheclCollection extends ArrayCollection
{
    public function est_valide(): bool
    {
        foreach (Mois::cases() as $mois)
            if (null === $this->findFirst(fn(mixed $key, Nhecl $item): bool => $item->mois === $mois))
                return false;

        return true;
    }

    public function nhecl(Mois $mois): float
    {
        return $this
            ->filter(fn(Nhecl $item): bool => $item->mois === $mois)
            ->reduce(fn(float $carry, Nhecl $item): float => $carry += $item->nhecl);
    }
}
