<?php

namespace App\Domain\Audit\Data;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Mois;

/**
 * @property SollicitationExterieure[] $elements
 */
final class SollicitationExterieureCollection extends ArrayCollection
{
    public function find(Mois $mois): ?SollicitationExterieure
    {
        return $this->findFirst(fn(mixed $key, SollicitationExterieure $item): bool => $item->mois === $mois);
    }

    public function est_valide(): bool
    {
        foreach (Mois::cases() as $mois)
            if (null === $this->find($mois)) return false;

        return true;
    }
}
