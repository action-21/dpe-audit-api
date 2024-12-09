<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum PositionChaudiere: string implements Enum
{
    case CHAUDIERE_MURALE = 'CHAUDIERE_MURALE';
    case CHAUDIERE_SOL = 'CHAUDIERE_SOL';

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
