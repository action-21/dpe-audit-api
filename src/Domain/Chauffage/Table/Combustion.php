<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class Combustion implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly string $rpn,
        public readonly ?string $rpint,
        public readonly ?string $qp0,
        public readonly ?float $pveil,
        public readonly int|float $pn_max,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
