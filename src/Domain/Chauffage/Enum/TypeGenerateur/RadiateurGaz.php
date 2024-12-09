<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum RadiateurGaz: string
{
    case RADIATEUR_GAZ = 'RADIATEUR_GAZ';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
