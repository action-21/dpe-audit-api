<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\EtatIsolation;

/**
 * @property Paroi[] $elements
 */
abstract class ParoiCollection extends ArrayCollection
{
    public function find(Id $id): ?Paroi
    {
        return array_find(
            $this->elements,
            fn(Paroi $item): bool => $item->id()->compare($id)
        );
    }

    abstract public function with_isolation(EtatIsolation $isolation): static;

    public function with_local_non_chauffe(Id $id): static
    {
        return $this->filter(
            fn(Paroi $item): bool => $item->local_non_chauffe()?->id()->compare($id) ?? false
        );
    }

    public function with_paroi(Id $id): static
    {
        return $this->filter(
            fn(Paroi $item): bool => $item->paroi()?->id()->compare($id) ?? false
        );
    }

    public function surface(): float
    {
        return $this->reduce(
            fn(float $surface, Paroi $item) => $surface + $item->surface()
        );
    }

    public function surface_reference(): float
    {
        return $this->reduce(
            fn(float $surface, Paroi $item) => $surface + $item->surface_reference()
        );
    }
}
