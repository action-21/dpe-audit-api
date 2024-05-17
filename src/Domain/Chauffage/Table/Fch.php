<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Fch implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $fch,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
