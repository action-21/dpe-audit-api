<?php

namespace App\Domain\Ventilation\Enum\TypeGenerateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;

enum Vmr: string
{
    case VMR = 'VMR';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
