<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Rr implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $rr,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
