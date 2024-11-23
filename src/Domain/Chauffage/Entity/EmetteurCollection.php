<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;

/**
 * @property Emetteur[] $elements
 */
final class EmetteurCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Emetteur $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Emetteur $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Emetteur
    {
        return $this->findFirst(fn(mixed $key, Emetteur $item): bool => $item->id()->compare($id));
    }
}
