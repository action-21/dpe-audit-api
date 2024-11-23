<?php

namespace App\Domain\Ecs\Enum\EnergieGenerateur;

use App\Domain\Ecs\Enum\EnergieGenerateur;

enum EnergieChaudiereMultiBatiment: string
{
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';
    case FIOUL = 'FIOUL';
    case CHARBON = 'CHARBON';
    case BOIS = 'BOIS';

    public function to(): EnergieGenerateur
    {
        return EnergieGenerateur::from($this->value);
    }
}
