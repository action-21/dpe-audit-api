<?php

namespace App\Domain\MasqueProche\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Table\TableValue;
use App\Domain\MasqueProche\Enum\TypeMasqueProche;

class Fe1 implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly TypeMasqueProche $type_masque_proche,
        public readonly ?Orientation $orientation,
        public readonly ?float $avancee_defaut,
        public readonly float $fe1,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->fe1;
    }
}
