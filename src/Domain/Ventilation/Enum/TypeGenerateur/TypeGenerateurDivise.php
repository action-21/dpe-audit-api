<?php

namespace App\Domain\Ventilation\Enum\TypeGenerateur;

use App\Domain\Ventilation\Enum\TypeGenerateur;

enum TypeGenerateurDivise: string
{
    case VMI = 'VMI';
    case VMR = 'VMR';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}