<?php

namespace App\Domain\Visite\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Logement[] $elements
 */
final class LogementCollection extends ArrayCollection
{
    public function controle(): void
    {
        $this->walk(fn(Logement $item) => $item->controle());
    }

    public function find(Id $id): ?Logement
    {
        return $this->findFirst(fn(mixed $key, Logement $item): bool => $item->id()->compare($id));
    }
}
