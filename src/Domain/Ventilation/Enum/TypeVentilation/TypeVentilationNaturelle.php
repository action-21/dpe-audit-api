<?php

namespace App\Domain\Ventilation\Enum\TypeVentilation;

use App\Domain\Ventilation\Enum\TypeVentilation;

enum TypeVentilationNaturelle: string
{
    case OUVERTURE_FENETRES = 'OUVERTURE_FENETRES';
    case ENTREES_AIR_HAUTES_ET_BASSES = 'ENTREES_AIR_HAUTES_ET_BASSES';

    public function to(): TypeVentilation
    {
        return TypeVentilation::from($this->value);
    }
}
