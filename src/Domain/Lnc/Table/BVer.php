<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Table\TableValue;

class BVer implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly Orientation $orientation,
        public readonly float $bver,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function orientation(): Orientation
    {
        return $this->orientation;
    }

    public function valeur(): float
    {
        return $this->bver;
    }
}
