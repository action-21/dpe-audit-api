<?php

namespace App\Domain\Baie\Enum\TypeMasqueProche;

use App\Domain\Baie\Enum\TypeMasqueProche;

enum BalconAuvent: string
{
    case FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS = 'FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS';
    case BALCON_OU_AUVENT = 'BALCON_OU_AUVENT';

    public function to(): TypeMasqueProche
    {
        return TypeMasqueProche::from($this->value);
    }
}
