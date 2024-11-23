<?php

namespace App\Domain\Enveloppe\Enum\Inertie;

use App\Domain\Enveloppe\Enum\Inertie;

enum InertieRefend: string
{
    case INCONNUE = 'INCONNUE';
    case LOURDE = 'LOURDE';
    case LEGERE = 'LEGERE';

    public function to(): Inertie
    {
        return Inertie::from($this->value);
    }

    public function choices(): array
    {
        return \array_column(self::cases(), 'value');
    }
}
