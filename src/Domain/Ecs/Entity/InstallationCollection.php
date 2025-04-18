<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @var Installation[] $elements
 */
final class InstallationCollection extends ArrayCollection
{
    public function reinitialise(): void
    {
        $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Installation
    {
        return array_find($this->elements, fn(Installation $item) => $item->id()->compare($id));
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Installation $item): float => $carry += $item->surface());
    }

    public function with_generateur(Id $id): static
    {
        return $this->filter(fn(Installation $item) => $item->systemes()->has_generateur($id));
    }

    public function has_generateur(Id $id): bool
    {
        return $this->with_generateur($id)->count() > 0;
    }
}
