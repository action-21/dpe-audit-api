<?php

namespace App\Domain\Enveloppe\Entity\Lnc;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Mitoyennete;

/**
 * @property ParoiOpaque[] $elements
 */
final class ParoiOpaqueCollection extends ArrayCollection
{
    public function reinitialise(): static
    {
        return $this->walk(fn(ParoiOpaque $item) => $item->reinitialise());
    }

    public function find(Id $id): ?ParoiOpaque
    {
        return array_find(
            $this->elements,
            fn(ParoiOpaque $item): bool => $item->id()->compare($id)
        );
    }

    public function with_mitoyennetes(Mitoyennete ...$mitoyennetes): self
    {
        return $this->filter(
            fn(ParoiOpaque $item): bool => in_array($item->position()->mitoyennete, $mitoyennetes)
        );
    }

    public function surface(): float
    {
        return $this->reduce(
            fn(float $surface, ParoiOpaque $item): float => $surface + $item->position()->surface
        );
    }

    public function surface_opaque(): float
    {
        return $this->reduce(
            fn(float $surface, ParoiOpaque $item): float => $surface + $item->surface_opaque()
        );
    }
}
