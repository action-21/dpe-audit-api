<?php

namespace App\Domain\Refend;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Refend[] $elements
 */
final class RefendCollection extends ArrayCollection
{
    public function find(Id $id): ?Refend
    {
        return $this->findFirst(fn (mixed $key, Refend $item): bool => $item->id()->compare($id));
    }

    public function search_by_refend_lourd(): self
    {
        return $this->filter(fn (Refend $item): bool => $item->refend_lourd());
    }
}
