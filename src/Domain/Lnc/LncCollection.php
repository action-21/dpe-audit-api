<?php

namespace App\Domain\Lnc;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Enum\TypeLnc;

/**
 * @property Lnc[] $elements
 */
final class LncCollection extends ArrayCollection
{
    public function find(Id $id): ?Lnc
    {
        return $this->findFirst(fn (mixed $key, Lnc $item): bool => $item->id()->compare($id));
    }

    public function search_by_type_lnc(TypeLnc $type_lnc): self
    {
        return $this->filter(fn (Lnc $item): bool => $item->type_lnc() === $type_lnc);
    }

    public function search_by_surface_paroi(float $surface_aue): self
    {
        return $this->filter(fn (Lnc $item): bool => $item->surface_aue() === $surface_aue);
    }
}
