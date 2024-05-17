<?php

namespace App\Domain\Baie\Table;

use App\Domain\Common\Table\TableValue;

class Ug implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly ?float $epaisseur_lame,
        public readonly float $ug
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->ug;
    }
}
