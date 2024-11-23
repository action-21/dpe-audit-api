<?php

namespace App\Domain\Chauffage\Enum\EnergieGenerateur;

use App\Domain\Chauffage\Enum\EnergieGenerateur;

enum EnergieChaudiere: string 
{
    case ELECTRICITE = 'ELECTRICITE';
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';
    case FIOUL = 'FIOUL';
    case CHARBON = 'CHARBON';
    case BOIS_BUCHE = 'BOIS_BUCHE';
    case BOIS_PLAQUETTE = 'BOIS_PLAQUETTE';
    case BOIS_GRANULE = 'BOIS_GRANULE';

    public function to(): EnergieGenerateur
    {
        return EnergieGenerateur::from($this->value);
    }
}
