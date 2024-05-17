<?php

namespace App\Domain\Porte\Enum;

use App\Domain\Common\Enum\Enum;

enum TypePose: int implements Enum
{
    case NU_EXTERIEUR = 1;
    case NU_INTERIEUR = 2;
    case TUNNEL = 3;
    /** @deprecated */
    case SANS_OBJET = 4;

    public static function from_enum_type_pose_id(int $id): self
    {
        return self::from($id);
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::NU_EXTERIEUR => 'Nu extérieur',
            self::NU_INTERIEUR => 'Nu intérieur',
            self::TUNNEL => 'Tunnel',
            self::SANS_OBJET => 'Sans objet',
        };
    }
}
