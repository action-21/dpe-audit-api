<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum TypeChauffeEauElectrique: string
{
    case BALLON_ELECTRIQUE_HORIZONTAL = 'BALLON_ELECTRIQUE_HORIZONTAL';
    case BALLON_ELECTRIQUE_VERTICAL = 'BALLON_ELECTRIQUE_VERTICAL';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
