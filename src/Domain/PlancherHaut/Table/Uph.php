<?php

namespace App\Domain\PlancherHaut\Table;

use App\Domain\Common\Table\TableValue;

/**
 * Valeur forfaitaire du coefficient de transmission thermique d'un plancher haut
 */
class Uph implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $uph,
        public readonly bool $effet_joule,
        public readonly bool $combles,
        public readonly bool $terrasse,
        public readonly int $tv_uph_id,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->uph;
    }
}
