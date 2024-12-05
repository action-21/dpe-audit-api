<?php

namespace App\Domain\Refroidissement\Enum\TypeGenerateur;

use App\Domain\Refroidissement\Enum\TypeGenerateur;

enum TypeThermodynamique: string
{
    case PAC_AIR_AIR = 'PAC_AIR_AIR';
    case PAC_AIR_EAU = 'PAC_AIR_EAU';
    case PAC_EAU_EAU = 'PAC_EAU_EAU';
    case PAC_EAU_GLYCOLEE_EAU = 'PAC_EAU_GLYCOLEE_EAU';
    case PAC_GEOTHERMIQUE = 'PAC_GEOTHERMIQUE';
    case AUTRE_SYSTEME_THERMODYNAMIQUE = 'AUTRE_SYSTEME_THERMODYNAMIQUE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
