<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Enum\TypeVentilation;

/**
 * @property Systeme[] $elements
 */
final class SystemeCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Systeme
    {
        return array_find(
            $this->elements,
            fn(Systeme $item): bool => $item->id()->compare($id),
        );
    }

    public function with_installation(Id $id): self
    {
        return $this->filter(
            fn(Systeme $item): bool => $item->installation()->id()->compare($id)
        );
    }

    public function with_generateur(Id $id): self
    {
        return $this->filter(
            fn(Systeme $item): bool => $item->generateur()?->id()->compare($id) ?? false
        );
    }
}
