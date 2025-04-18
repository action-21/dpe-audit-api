<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum TypePose: string implements Enum
{
    case NU_EXTERIEUR = 'nu_exterieur';
    case NU_INTERIEUR = 'nu_interieur';
    case TUNNEL = 'tunnel';

    public static function from_enum_type_pose_id(int $id): self
    {
        return match ($id) {
            1 => self::NU_EXTERIEUR,
            2 => self::NU_INTERIEUR,
            3 => self::TUNNEL,
        };
    }

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
