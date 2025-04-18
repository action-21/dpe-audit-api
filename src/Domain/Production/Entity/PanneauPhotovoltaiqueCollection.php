<?php

namespace App\Domain\Production\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property PanneauPhotovoltaique[] $elements
 */
final class PanneauPhotovoltaiqueCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(PanneauPhotovoltaique $item) => $item->reinitialise());
    }

    public function find(Id $id): ?PanneauPhotovoltaique
    {
        return array_find(
            $this->elements,
            fn(PanneauPhotovoltaique $item) => $item->id()->compare($id),
        );
    }
}
