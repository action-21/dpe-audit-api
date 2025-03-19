<?php

namespace App\Domain\Enveloppe\Enum\Inertie;

use App\Domain\Enveloppe\Enum\Inertie;

enum InertieParoi: string
{
    case LOURDE = 'lourde';
    case LEGERE = 'legere';

    public function to(): Inertie
    {
        return Inertie::from($this->value);
    }
}
