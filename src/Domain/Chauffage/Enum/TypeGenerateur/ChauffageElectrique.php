<?php

namespace App\Domain\Chauffage\Enum\TypeGenerateur;

use App\Domain\Chauffage\Enum\TypeGenerateur;

enum ChauffageElectrique: string
{
    case RADIATEUR_ELECTRIQUE_ACCUMULATION = 'RADIATEUR_ELECTRIQUE_ACCUMULATION';
    case RADIATEUR_ELECTRIQUE = 'RADIATEUR_ELECTRIQUE';
    case PANNEAU_RAYONNANT_ELECTRIQUE = 'PANNEAU_RAYONNANT_ELECTRIQUE';
    case PLANCHER_RAYONNANT_ELECTRIQUE = 'PLANCHER_RAYONNANT_ELECTRIQUE';
    case PLAFOND_RAYONNANT_ELECTRIQUE = 'PLAFOND_RAYONNANT_ELECTRIQUE';
    case CONVECTEUR_BI_JONCTION = 'CONVECTEUR_BI_JONCTION';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
