<?php

namespace App\Domain\Refroidissement\Enum\TypeGenerateur;

use App\Domain\Refroidissement\Enum\TypeGenerateur;

enum ReseauFroid: string
{
    case RESEAU_FROID = 'RESEAU_FROID';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
