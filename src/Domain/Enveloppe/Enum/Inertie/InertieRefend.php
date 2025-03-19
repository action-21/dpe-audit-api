<?php

namespace App\Domain\Enveloppe\Enum\Inertie;

use App\Domain\Enveloppe\Enum\Inertie;

enum InertieRefend: string
{
    case LOURDE = 'lourde';
    case LEGERE = 'legere';

    public function to(): Inertie
    {
        return Inertie::from($this->value);
    }

    public function choices(): array
    {
        return \array_column(self::cases(), 'value');
    }
}
