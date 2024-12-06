<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum PoeleBouilleur: string
{
    case POELE_BOUILLEUR = 'POELE_BOUILLEUR';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
