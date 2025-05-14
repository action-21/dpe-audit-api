<?php

namespace App\Domain\Audit\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Logement>
 */
final class LogementCollection extends ArrayCollection
{
    public function find(Id $id): ?Logement
    {
        return array_find(
            $this->elements,
            fn(Logement $item): bool => $item->id()->compare($id)
        );
    }
}
