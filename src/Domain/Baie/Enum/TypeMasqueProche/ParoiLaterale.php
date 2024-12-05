<?php

namespace App\Domain\Baie\Enum\TypeMasqueProche;

use App\Domain\Baie\Enum\TypeMasqueProche;

enum ParoiLaterale: string
{
    case PAROI_LATERALE_SANS_OBSTACLE_AU_SUD = 'PAROI_LATERALE_SANS_OBSTACLE_AU_SUD';
    case PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD = 'PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD';

    public function to(): TypeMasqueProche
    {
        return TypeMasqueProche::from($this->value);
    }
}
