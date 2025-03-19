<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Refend[] $elements
 */
final class RefendCollection extends ArrayCollection
{
    public function controle(): void
    {
        $this->walk(fn(Refend $item) => $item->controle());
    }

    public function find(Id $id): ?Refend
    {
        return $this->findFirst(fn(mixed $key, Refend $item): bool => $item->id()->compare($id));
    }

    public function filter_by_inertie(bool $est_lourd): self
    {
        return $this->filter(fn(Refend $item): bool => $item->est_lourd() === $est_lourd);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Refend $item): float => $carry += $item->surface());
    }
}
