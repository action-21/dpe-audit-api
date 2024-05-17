<?php

namespace App\Domain\PontThermique;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property PontThermique[] $elements
 */
final class PontThermiqueCollection extends ArrayCollection
{
    public function find(Id $id): ?PontThermique
    {
        return $this->filter(fn (PontThermique $item): bool => $item->id()->compare($id))->first();
    }
}
