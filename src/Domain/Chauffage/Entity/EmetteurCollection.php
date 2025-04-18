<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Emetteur[] $elements
 */
final class EmetteurCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Emetteur $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Emetteur
    {
        return array_find(
            $this->elements,
            fn(Emetteur $item): bool => $item->id()->compare($id),
        );
    }

    public function with_installation(Id $id): static
    {
        return $this->filter(
            fn(Emetteur $item): bool => $item->installations()->find($id) !== null,
        );
    }

    public function with_generateur(Id $id): static
    {
        return $this->filter(
            fn(Emetteur $item): bool => $item->chauffage()->systemes()
                ->with_emetteur($item->id())
                ->with_generateur($id)
                ->count() > 0,
        );
    }
}
