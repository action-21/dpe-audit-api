<?php

namespace App\Domain\Ventilation\Enum\TypeGenerateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;

enum TypeGenerateurCentralise: string
{
    case VMC_SIMPLE_FLUX = 'VMC_SIMPLE_FLUX';
    case VMC_SIMPLE_FLUX_GAZ = 'VMC_SIMPLE_FLUX_GAZ';
    case VMC_BASSE_PRESSION = 'VMC_BASSE_PRESSION';
    case VMC_DOUBLE_FLUX = 'VMC_DOUBLE_FLUX';
    case VENTILATION_HYBRIDE = 'VENTILATION_HYBRIDE';
    case VENTILATION_MECANIQUE = 'VENTILATION_MECANIQUE';
    case PUIT_CLIMATIQUE = 'PUIT_CLIMATIQUE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
