<?php

namespace App\Domain\PlancherHaut\Table;

use App\Domain\Common\Table\TableValue;
use App\Domain\PlancherHaut\Enum\TypePlancherHaut;

/**
 * Valeur forfaitaire du coefficient de transmission thermique d'un plancher haut non isolé
 */
class Uph0 implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly TypePlancherHaut $type_plancher_haut,
        public readonly float $uph0,
        public readonly int $tv_uph0_id,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->uph0;
    }
}
