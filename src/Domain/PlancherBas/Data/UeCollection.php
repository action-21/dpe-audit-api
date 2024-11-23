<?php

namespace App\Domain\PlancherBas\Data;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Ue[] $elements
 */
final class UeCollection extends ArrayCollection
{
    public function first(): ?Ue
    {
        return parent::first();
    }

    public function find_by(float $surface, float $perimetre): ?Ue
    {
        if ($surface <= 0 || $perimetre <= 0) {
            return null;
        }
        $_2sp = \round(2 * $surface / $perimetre);
        return $this->findFirst(fn(Ue $item): bool => $item->_2sp === $_2sp);
    }

    public function valeurs_proches(float $surface, float $perimetre): self
    {
        if ($surface <= 0 || $perimetre <= 0) {
            return static::createFrom([]);
        }
        $_2sp = \round(2 * $surface / $perimetre);
        return $this
            ->usort(fn(Ue $a, Ue $b): int => \round(\abs($a->_2sp - $_2sp) - \abs($b->_2sp - $_2sp)))
            ->slice(0, 2);
    }
}
