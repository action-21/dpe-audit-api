<?php

namespace App\Domain\Enveloppe\Table;

use App\Domain\Common\Table\TableValue;

class Q4paConv implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $q4pa_conv,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->q4pa_conv;
    }
}
