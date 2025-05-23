<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Installation>
 */
final class InstallationCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Installation
    {
        return array_find($this->elements, fn(Installation $item): bool => $item->id()->compare($id));
    }

    public function surface(): float
    {
        return $this->reduce(
            fn(float $surface, Installation $item): float => $surface += $item->surface()
        );
    }
}
