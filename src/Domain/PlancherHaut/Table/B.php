<?php

namespace App\Domain\PlancherHaut\Table;

use App\Domain\Common\Table\TableValue;

class B implements TableValue
{
    public function __construct(public readonly int $id, public readonly float $b)
    {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->b;
    }
}
