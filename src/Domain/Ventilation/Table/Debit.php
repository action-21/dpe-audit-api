<?php

namespace App\Domain\Ventilation\Table;

use App\Domain\Common\Table\TableValue;

class Debit implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $qvarep_conv,
        public readonly float $qvasouf_conv,
        public readonly float $smea_conv,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function qvarep_conv(): float
    {
        return $this->qvarep_conv;
    }

    public function qvasouf_conv(): float
    {
        return $this->qvasouf_conv;
    }

    public function smea_conv(): float
    {
        return $this->smea_conv;
    }
}
