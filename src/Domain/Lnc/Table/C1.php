<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Table\TableValue;

/**
 * Coefficients d'orientation et d'inclinaison des parois vitrées
 */
class C1 implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly Mois $mois,
        public readonly float $c1,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function mois(): Mois
    {
        return $this->mois;
    }

    public function valeur(): float
    {
        return $this->c1;
    }
}
