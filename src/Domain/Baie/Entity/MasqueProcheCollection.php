<?php

namespace App\Domain\Baie\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property MasqueProche[] $elements
 */
final class MasqueProcheCollection extends ArrayCollection
{
    public function find(Id $id): ?MasqueProche
    {
        return $this->findFirst(fn(mixed $key, MasqueProche $item): bool => $item->id()->compare($id));
    }
}
