<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @var Systeme[] $elements
 */
final class SystemeCollection extends ArrayCollection
{
    public function reinitialise(): void
    {
        $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Systeme
    {
        return array_find($this->elements, fn(Systeme $item) => $item->id()->compare($id));
    }

    public function with_installation(Id $id): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->installation()->id()->compare($id));
    }

    public function with_generateur(Id $id): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->id()->compare($id));
    }

    public function has_generateur(Id $id): bool
    {
        return $this->with_generateur($id)->count() > 0;
    }
}
