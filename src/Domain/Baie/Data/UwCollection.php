<?php

namespace App\Domain\Baie\Data;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Uw[] $elements
 */
final class UwCollection extends ArrayCollection
{
    public function first(): ?Uw
    {
        return parent::first();
    }

    public function last(): ?Uw
    {
        return parent::last();
    }

    public function find_by(float $ug): ?Uw
    {
        return $this->findFirst(fn(mixed $key, Uw $item): bool => $item->ug === $ug);
    }

    public function valeurs_proches(float $ug): self
    {
        return $this
            ->usort(fn(Uw $a, Uw $b): int => \round(\abs($a->ug - $ug) - \abs($b->ug - $ug)))
            ->slice(0, 2);
    }
}
