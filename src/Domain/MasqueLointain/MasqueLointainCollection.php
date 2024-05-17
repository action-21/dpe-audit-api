<?php

namespace App\Domain\MasqueLointain;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;

/**
 * @property MasqueLointain[] $elements
 */
final class MasqueLointainCollection extends ArrayCollection
{
    public function find(Id $id): ?MasqueLointain
    {
        return $this->findFirst(fn (mixed $key, MasqueLointain $item): bool => $item->id()->compare($id));
    }

    public function search_by_orientation(Orientation $orientation): self
    {
        return $this->filter(fn (MasqueLointain $item): bool => $item->orientation()->enum() === $orientation);
    }
}
