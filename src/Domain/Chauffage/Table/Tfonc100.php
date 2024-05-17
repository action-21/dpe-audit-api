<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Tfonc100 implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $tfonc100,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
