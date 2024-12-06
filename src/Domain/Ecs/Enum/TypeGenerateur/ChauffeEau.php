<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum ChauffeEau: string
{
    case ACCUMULATEUR = 'ACCUMULATEUR';
    case CHAUFFE_EAU_INSTANTANE = 'CHAUFFE_EAU_INSTANTANE';
    case CHAUFFE_EAU_VERTICAL = 'CHAUFFE_EAU_VERTICAL';
    case CHAUFFE_EAU_HORIZONTAL = 'CHAUFFE_EAU_HORIZONTAL';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
