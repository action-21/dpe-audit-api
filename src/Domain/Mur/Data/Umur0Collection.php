<?php

namespace App\Domain\Mur\Data;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Umur0[] $elements
 */
final class Umur0Collection extends ArrayCollection
{
    public function first(): ?Umur0
    {
        return parent::first();
    }

    public function last(): ?Umur0
    {
        return parent::last();
    }

    public function find_by_epaisseur(float $epaisseur): ?Umur0
    {
        return $this->findFirst(fn(mixed $key, Umur0 $item): bool => $item->epaisseur === $epaisseur);
    }

    public function valeurs_proches(float $epaisseur): self
    {
        if ($valeur = $this->find_by_epaisseur($epaisseur))
            return static::createFrom([$valeur]);

        return $this
            ->usort(fn(Umur0 $a, Umur0 $b): int => \round(\abs($a->epaisseur - $epaisseur) - \abs($b->epaisseur - $epaisseur)))
            ->slice(0, 2);
    }
}
