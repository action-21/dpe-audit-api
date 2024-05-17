<?php

namespace App\Domain\MasqueLointain\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Table\TableValue;
use App\Domain\MasqueLointain\Enum\SecteurOrientation;

final class Omb implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly SecteurOrientation $secteur_orientation,
        public readonly Orientation $orientation,
        public readonly float $hauteur_alpha_defaut,
        public readonly float $omb,
        public readonly int $tv_coef_masque_lointain_non_homogene_id,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->omb;
    }
}
