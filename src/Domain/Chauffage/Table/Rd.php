<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Rd implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $rd,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
