<?php

namespace App\Domain\Ventilation\Enum\TypeGenerateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;

enum VentilationMecanique: string
{
    case VENTILATION_MECANIQUE = 'VENTILATION_MECANIQUE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
