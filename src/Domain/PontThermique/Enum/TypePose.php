<?php

namespace App\Domain\PontThermique\Enum;

use App\Domain\Common\Enum\Enum;

enum TypePose: string implements Enum
{
    case NU_EXTERIEUR = 'NU_EXTERIEUR';
    case NU_INTERIEUR = 'NU_INTERIEUR';
    case TUNNEL = 'TUNNEL';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::NU_EXTERIEUR => 'Nu extérieur',
            self::NU_INTERIEUR => 'Nu intérieur',
            self::TUNNEL => 'Tunnel',
        };
    }
}
