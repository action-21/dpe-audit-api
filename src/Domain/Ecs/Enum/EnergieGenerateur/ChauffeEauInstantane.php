<?php

namespace App\Domain\Ecs\Enum\EnergieGenerateur;

use App\Domain\Ecs\Enum\EnergieGenerateur;

enum ChauffeEauInstantane: string 
{
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';

    public function to(): EnergieGenerateur
    {
        return EnergieGenerateur::from($this->value);
    }
}
