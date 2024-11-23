<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum TypeGenerateurAirChaud: string
{
    case GENERATEUR_AIR_CHAUD = 'GENERATEUR_AIR_CHAUD';
    case GENERATEUR_AIR_CHAUD_CONDENSATION = 'GENERATEUR_AIR_CHAUD_CONDENSATION';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
