<?php

namespace App\Domain\Baie\Enum\TypeBaie;

use App\Domain\Baie\Enum\TypeBaie;

enum Fenetre: string
{
    case FENETRE_BATTANTE = 'FENETRE_BATTANTE';
    case FENETRE_COULISSANTE = 'FENETRE_COULISSANTE';

    public function type_baie(): TypeBaie
    {
        return TypeBaie::from($this->value);
    }
}
