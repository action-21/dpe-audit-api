<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeChaudiere: string implements Enum
{
    case CHAUDIERE_MURALE = 'chaudiere_murale';
    case CHAUDIERE_SOL = 'chaudiere_sol';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this->value) {
            self::CHAUDIERE_MURALE => 'Chaudière murale',
            self::CHAUDIERE_SOL => 'Chaudière au sol',
        };
    }
}
