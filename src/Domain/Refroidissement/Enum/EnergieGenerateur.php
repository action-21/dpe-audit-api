<?php

namespace App\Domain\Refroidissement\Enum;

use App\Domain\Common\Enum\Energie;

enum EnergieGenerateur: string
{
    case ELECTRICITE = 'ELECTRICITE';
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';
    case RESEAU_FROID = 'RESEAU_FROID';

    public function to(): Energie
    {
        return Energie::from($this->value);
    }
}
