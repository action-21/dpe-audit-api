<?php

namespace App\Domain\Baie\Data;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Ug[] $elements
 */
final class UgCollection extends ArrayCollection
{
    public function first(): ?Ug
    {
        return parent::first();
    }

    public function last(): ?Ug
    {
        return parent::last();
    }

    public function find_by(float $ug): ?Ug
    {
        return $this->findFirst(fn(mixed $key, Ug $item): bool => $item->ug === $ug);
    }

    public function valeurs_proches(?float $epaisseur_lame = null): self
    {
        return $this
            ->usort(fn(Ug $a, Ug $b): int => \round(\abs($a->epaisseur_lame - $epaisseur_lame) - \abs($b->epaisseur_lame - $epaisseur_lame)))
            ->slice(0, 2);
    }
}
