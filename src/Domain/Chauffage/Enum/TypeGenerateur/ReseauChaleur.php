<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum ReseauChaleur: string
{
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
