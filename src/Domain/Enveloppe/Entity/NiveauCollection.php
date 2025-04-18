<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Inertie;

/**
 * @property Niveau[] $elements
 */
final class NiveauCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Niveau $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Niveau
    {
        return array_find(
            $this->elements,
            fn(Niveau $item): bool => $item->id()->compare($id)
        );
    }

    public function with_inertie(Inertie $inertie): static
    {
        return $this->filter(
            fn(Niveau $item): bool => $item->data()->inertie === $inertie
        );
    }

    public function surface(): float
    {
        return $this->reduce(
            fn(float $surface, Niveau $item) => $surface + $item->surface()
        );
    }
}
