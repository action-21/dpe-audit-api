<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum ReseauChaleur: string
{
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
