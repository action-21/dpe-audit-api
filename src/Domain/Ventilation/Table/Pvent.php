<?php

namespace App\Domain\Ventilation\Table;

use App\Domain\Common\Table\TableValue;

final class Pvent implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $ratio_utilisation,
        public readonly ?float $qvarep_conv,
        public readonly ?float $pvent_moy,
        public readonly ?float $pvent,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
