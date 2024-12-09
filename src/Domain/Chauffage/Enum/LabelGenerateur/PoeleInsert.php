<?php

namespace App\Domain\Chauffage\Enum\LabelGenerateur;

use App\Domain\Chauffage\Enum\LabelGenerateur;

enum PoeleInsert: string
{
    case FLAMME_VERTE = 'FLAMME_VERTE';
    case SANS = 'SANS';
    case INCONNU = 'INCONNU';

    public function to(): LabelGenerateur
    {
        return LabelGenerateur::from($this->value);
    }
}
