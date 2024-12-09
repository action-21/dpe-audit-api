<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum GenerateurAirChaud: string
{
    case GENERATEUR_AIR_CHAUD = 'GENERATEUR_AIR_CHAUD';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
