<?php

namespace App\Domain\Refroidissement\Enum\TypeGenerateur;

use App\Domain\Refroidissement\Enum\TypeGenerateur;

enum TypeAutres: string
{
    case AUTRE_SYSTEME_THERMODYNAMIQUE = 'AUTRE_SYSTEME_THERMODYNAMIQUE';
    case AUTRE = 'AUTRE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
