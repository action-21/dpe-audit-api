<?php

namespace App\Domain\Chauffage\Enum\EnergieGenerateur;

use App\Domain\Chauffage\Enum\EnergieGenerateur;

enum PoeleInsert: string
{
    case BOIS_BUCHE = 'BOIS_BUCHE';
    case BOIS_PLAQUETTE = 'BOIS_PLAQUETTE';
    case BOIS_GRANULE = 'BOIS_GRANULE';
    case FIOUL = 'FIOUL';
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';
    case CHARBON = 'CHARBON';

    public function to(): EnergieGenerateur
    {
        return EnergieGenerateur::from($this->value);
    }
}
