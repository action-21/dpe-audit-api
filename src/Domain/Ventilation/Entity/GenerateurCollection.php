<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;

/**
 * @property Generateur[] $elements
 */
final class GenerateurCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Generateur $item) => $item->reinitialise());
    }

    public function controle(): void
    {
        $this->walk(fn(Generateur $item) => $item->controle());
    }

    public function find(Id $id): ?Generateur
    {
        return $this->findFirst(fn(mixed $key, Generateur $item): bool => $item->id()->compare($id));
    }
}
