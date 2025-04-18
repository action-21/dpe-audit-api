<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum InertieParoi: string implements Enum
{
    case LOURDE = 'lourde';
    case LEGERE = 'legere';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::LOURDE => 'Lourde',
            self::LEGERE => 'Légère',
        };
    }
}
