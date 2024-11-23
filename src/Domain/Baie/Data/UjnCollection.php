<?php

namespace App\Domain\Baie\Data;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Ujn[] $elements
 */
final class UjnCollection extends ArrayCollection
{
    public function first(): ?Ujn
    {
        return parent::first();
    }

    public function last(): ?Ujn
    {
        return parent::last();
    }

    public function find_by(float $uw): ?Ujn
    {
        return $this->findFirst(fn(mixed $key, Ujn $item): bool => $item->uw === $uw);
    }

    public function valeurs_proches(float $uw): self
    {
        return $this
            ->usort(fn(Ujn $a, Ujn $b): int => \round(\abs($a->uw - $uw) - \abs($b->uw - $uw)))
            ->slice(0, 2);
    }
}
