<?php

namespace App\Domain\Baie\Enum\TypeBaie;

use App\Domain\Baie\Enum\TypeBaie;

enum ParoiVitree: string
{
    case BRIQUE_VERRE_PLEINE = 'BRIQUE_VERRE_PLEINE';
    case BRIQUE_VERRE_CREUSE = 'BRIQUE_VERRE_CREUSE';
    case POLYCARBONATE = 'POLYCARBONATE';

    public function type_baie(): TypeBaie
    {
        return TypeBaie::from($this->value);
    }
}
