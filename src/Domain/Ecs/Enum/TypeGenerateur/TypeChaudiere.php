<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum TypeChaudiere: string
{
    case CHAUDIERE_STANDARD = 'CHAUDIERE_STANDARD';
    case CHAUDIERE_BASSE_TEMPERATURE = 'CHAUDIERE_BASSE_TEMPERATURE';
    case CHAUDIERE_CONDENSATION = 'CHAUDIERE_CONDENSATION';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
