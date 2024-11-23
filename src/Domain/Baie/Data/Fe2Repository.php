<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Common\Enum\Orientation;

interface Fe2Repository
{
    public function find_by(
        TypeMasqueLointain $type_masque_lointain,
        Orientation $orientation_baie,
        float $hauteur_masque_alpha,
    ): ?Fe2;
}
