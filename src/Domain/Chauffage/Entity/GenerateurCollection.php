<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Generateur[] $elements
 */
final class GenerateurCollection extends ArrayCollection
{
    public function reinitialise(): void
    {
        $this->walk(fn(Generateur $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Generateur
    {
        return array_find(
            $this->elements,
            fn(Generateur $item): bool => $item->id()->compare($id),
        );
    }

    public function with_type(TypeGenerateur $type): self
    {
        return $this->filter(fn(Generateur $generateur) => $generateur->type() === $type);
    }
}
