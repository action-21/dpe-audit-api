<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum TypePacHybride: string
{
    case PAC_HYBRIDE_AIR_EAU = 'PAC_HYBRIDE_AIR_EAU';
    case PAC_HYBRIDE_EAU_EAU = 'PAC_HYBRIDE_EAU_EAU';
    case PAC_HYBRIDE_EAU_GLYCOLEE_EAU = 'PAC_HYBRIDE_EAU_GLYCOLEE_EAU';
    case PAC_HYBRIDE_GEOTHERMIQUE = 'PAC_HYBRIDE_GEOTHERMIQUE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
