<?php

namespace App\Domain\Ecs\Enum\EnergieGenerateur;

use App\Domain\Ecs\Enum\EnergieGenerateur;

enum PoeleBouilleur: string 
{
    case BOIS_BUCHE = 'BOIS_BUCHE';
    case BOIS_PLAQUETTE = 'BOIS_PLAQUETTE';
    case BOIS_GRANULE = 'BOIS_GRANULE';

    public function to(): EnergieGenerateur
    {
        return EnergieGenerateur::from($this->value);
    }
}
