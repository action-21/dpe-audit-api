<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum Chaudiere: string
{
    case CHAUDIERE = 'CHAUDIERE';
    case CHAUDIERE_MULTI_BATIMENT = 'CHAUDIERE_MULTI_BATIMENT';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
