<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum Pac: string
{
    case CET_AIR_AMBIANT = 'CET_AIR_AMBIANT';
    case CET_AIR_EXTERIEUR = 'CET_AIR_EXTERIEUR';
    case CET_AIR_EXTRAIT = 'CET_AIR_EXTRAIT';
    case PAC_DOUBLE_SERVICE = 'PAC_DOUBLE_SERVICE';
    case PAC_MULTI_BATIMENT = 'PAC_MULTI_BATIMENT';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
