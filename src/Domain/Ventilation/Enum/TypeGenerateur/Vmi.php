<?php

namespace App\Domain\Ventilation\Enum\TypeGenerateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;

enum Vmi: string
{
    case VMI = 'VMI';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
