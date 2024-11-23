<?php

namespace App\Domain\Enveloppe\Enum\Inertie;

use App\Domain\Enveloppe\Enum\Inertie;

enum InertiePlancherIntermediaire: string
{
    case INCONNUE = 'INCONNUE';
    case LOURDE = 'LOURDE';
    case LEGERE = 'LEGERE';

    public function to(): Inertie
    {
        return Inertie::from($this->value);
    }
}
