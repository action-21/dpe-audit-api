<?php

namespace App\Domain\PlancherBas\Table;

use App\Domain\Common\Table\TableValue;

/**
 * Valeur forfaitaire du coefficient de transmission thermique d'un plancher bas sur terre plein
 */
final class Ue implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly float $upb,
        public readonly float $_2sp,
        public readonly float $ue,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function x(): float
    {
        return $this->upb;
    }

    public function y(): float
    {
        return $this->_2sp;
    }

    public function valeur(): float
    {
        return $this->ue;
    }
}
