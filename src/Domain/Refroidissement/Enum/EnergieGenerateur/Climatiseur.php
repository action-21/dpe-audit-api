<?php

namespace App\Domain\Refroidissement\Enum\EnergieGenerateur;

use App\Domain\Refroidissement\Enum\EnergieGenerateur;

enum Climatiseur: string
{
    case ELECTRICITE = 'ELECTRICITE';
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';

    public function to(): EnergieGenerateur
    {
        return EnergieGenerateur::from($this->value);
    }
}
