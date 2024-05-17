<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Rg implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $rg,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
