<?php

namespace App\Domain\MasqueLointain\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Table\TableValue;

final class Fe2 implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly Orientation $orientation,
        public readonly float $hauteur_alpha_defaut,
        public readonly float $fe2,
        public readonly int $tv_coef_masque_lointain_homogene_id,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->fe2;
    }
}
