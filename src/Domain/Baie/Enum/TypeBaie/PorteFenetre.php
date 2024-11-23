<?php

namespace App\Domain\Baie\Enum\TypeBaie;

use App\Domain\Baie\Enum\TypeBaie;

enum PorteFenetre: string
{
    case PORTE_FENETRE_COULISSANTE = 'PORTE_FENETRE_COULISSANTE';
    case PORTE_FENETRE_BATTANTE = 'PORTE_FENETRE_BATTANTE';

    public function type_baie(): TypeBaie
    {
        return TypeBaie::from($this->value);
    }
}
