<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Common\Table\TableValue;

final class I0 implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $i0,
        public readonly int $tv_intermittence_id,
        public readonly ?bool $comptage_individuel = null,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
