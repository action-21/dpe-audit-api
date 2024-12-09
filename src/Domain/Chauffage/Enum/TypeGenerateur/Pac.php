<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum Pac: string
{
    case PAC_AIR_AIR = 'PAC_AIR_AIR';
    case PAC_AIR_EAU = 'PAC_AIR_EAU';
    case PAC_EAU_EAU = 'PAC_EAU_EAU';
    case PAC_EAU_GLYCOLEE_EAU = 'PAC_EAU_GLYCOLEE_EAU';
    case PAC_GEOTHERMIQUE = 'PAC_GEOTHERMIQUE';
    case PAC_MULTI_BATIMENT = 'PAC_MULTI_BATIMENT';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
