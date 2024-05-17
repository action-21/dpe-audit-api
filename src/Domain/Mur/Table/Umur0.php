<?php

namespace App\Domain\Mur\Table;

use App\Domain\Common\Table\TableValue;

class Umur0 implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly ?float $epaisseur,
        public readonly float $umur0
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function x(): ?float
    {
        return $this->epaisseur;
    }

    public function y(): float
    {
        return $this->umur0;
    }

    public function valeur(): float
    {
        return $this->umur0;
    }
}
