<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\{SecteurChampsVision, TypeMasqueLointain};
use App\Domain\Common\Enum\Orientation;

interface OmbRepository
{
    public function find_by(
        TypeMasqueLointain $type_masque_lointain,
        Orientation $orientation_baie,
        SecteurChampsVision $secteur,
        float $hauteur_masque_alpha,
    ): ?Omb;
}
