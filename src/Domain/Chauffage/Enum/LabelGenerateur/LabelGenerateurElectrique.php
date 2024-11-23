<?php

namespace App\Domain\Chauffage\Enum\LabelGenerateur;

use App\Domain\Chauffage\Enum\LabelGenerateur;

enum LabelGenerateurElectrique: string
{
    case NF_PERFORMANCE = 'NF_PERFORMANCE';
    case SANS = 'SANS';
    case INCONNU = 'INCONNU';

    public function to(): LabelGenerateur
    {
        return LabelGenerateur::from($this->value);
    }
}
