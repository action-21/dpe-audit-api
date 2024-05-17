<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Re implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $re,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
