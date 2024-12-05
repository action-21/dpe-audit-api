<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum ChauffeEauElectrique: string
{
    case BALLON_ELECTRIQUE_HORIZONTAL = 'BALLON_ELECTRIQUE_HORIZONTAL';
    case BALLON_ELECTRIQUE_VERTICAL = 'BALLON_ELECTRIQUE_VERTICAL';
    case CHAUFFE_EAU_INSTANTANE = 'CHAUFFE_EAU_INSTANTANE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
