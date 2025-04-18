<?php

namespace App\Domain\Enveloppe\Entity\Baie;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property MasqueProche[] $elements
 */
final class MasqueProcheCollection extends ArrayCollection
{
    public function find(Id $id): ?MasqueProche
    {
        return array_find(
            $this->elements,
            fn(MasqueProche $item): bool => $item->id()->compare($id)
        );
    }
}
