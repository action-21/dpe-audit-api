<?php

namespace App\Domain\Lnc\Enum\TypeBaie;

use App\Domain\Lnc\Enum\TypeBaie;

enum TypeBaieFenetre: string
{
    case FENETRE_BATTANTE = 'FENETRE_BATTANTE';
    case FENETRE_COULISSANTE = 'FENETRE_COULISSANTE';
    case PORTE_FENETRE_COULISSANTE = 'PORTE_FENETRE_COULISSANTE';
    case PORTE_FENETRE_BATTANTE = 'PORTE_FENETRE_BATTANTE';

    public function to(): TypeBaie
    {
        return TypeBaie::from($this->value);
    }
}
