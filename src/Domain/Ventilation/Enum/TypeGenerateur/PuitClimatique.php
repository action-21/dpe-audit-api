<?php

namespace App\Domain\Ventilation\Enum\TypeGenerateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;

enum PuitClimatique: string
{
    case PUIT_CLIMATIQUE = 'PUIT_CLIMATIQUE';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
