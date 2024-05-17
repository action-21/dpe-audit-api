<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Scop implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly ?float $cop,
        public readonly ?float $scop,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
