<?php

namespace App\Domain\Batiment\Table;

use App\Domain\Common\Table\TableValue;

/**
 * @see §18.2
 */
class Tbase implements TableValue
{
    public function __construct(public readonly int $id, public readonly float $tbase)
    {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->tbase;
    }
}
