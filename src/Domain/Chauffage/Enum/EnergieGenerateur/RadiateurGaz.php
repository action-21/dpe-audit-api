<?php

namespace App\Domain\Chauffage\Enum\EnergieGenerateur;

use App\Domain\Chauffage\Enum\EnergieGenerateur;

enum RadiateurGaz: string
{
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';

    public function to(): EnergieGenerateur
    {
        return EnergieGenerateur::from($this->value);
    }
}
