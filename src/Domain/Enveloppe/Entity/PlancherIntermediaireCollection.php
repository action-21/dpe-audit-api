<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property PlancherIntermediaire[] $elements
 */
final class PlancherIntermediaireCollection extends ArrayCollection
{
    public function controle(): void
    {
        $this->walk(fn(PlancherIntermediaire $item) => $item->controle());
    }

    public function find(Id $id): ?PlancherIntermediaire
    {
        return $this->findFirst(fn(mixed $key, PlancherIntermediaire $item): bool => $item->id()->compare($id));
    }

    public function filter_by_inertie(bool $est_lourd): self
    {
        return $this->filter(fn(PlancherIntermediaire $item): bool => $item->est_lourd() === $est_lourd);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, PlancherIntermediaire $item): float => $carry += $item->surface());
    }
}
