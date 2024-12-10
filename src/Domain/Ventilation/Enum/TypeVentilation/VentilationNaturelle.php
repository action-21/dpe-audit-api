<?php

namespace App\Domain\Ventilation\Enum\TypeVentilation;

use App\Domain\Ventilation\Enum\TypeVentilation;

enum VentilationNaturelle: string
{
    case VENTILATION_NATURELLE_OUVERTURE_FENETRES = 'VENTILATION_NATURELLE_OUVERTURE_FENETRES';
    case VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES = 'VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES';
    case VENTILATION_NATURELLE_CONDUIT = 'VENTILATION_NATURELLE_CONDUIT';
    case VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES = 'VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES';

    public function to(): TypeVentilation
    {
        return TypeVentilation::from($this->value);
    }
}
