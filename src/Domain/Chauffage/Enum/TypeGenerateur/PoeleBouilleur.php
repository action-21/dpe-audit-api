<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum PoeleBouilleur: string
{
    case POELE_BOUILLEUR = 'POELE_BOUILLEUR';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
