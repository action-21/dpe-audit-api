<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum TypePoeleInsert: string
{
    case CUISINIERE = 'CUISINIERE';
    case FOYER_FERME = 'FOYER_FERME';
    case INSERT = 'INSERT';
    case POELE = 'POELE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
