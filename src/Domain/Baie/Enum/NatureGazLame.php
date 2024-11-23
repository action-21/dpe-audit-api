<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;

enum NatureGazLame: string implements Enum
{
    case AIR = 'AIR';
    case ARGON = 'ARGON';
    case KRYPTON = 'KRYPTON';
    case INCONNU = 'INCONNU';

    public static function from_enum_type_gaz_lame_id(int $id): self
    {
        return match ($id) {
            1 => self::AIR,
            2 => self::ARGON,
            3 => self::INCONNU
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::AIR => 'Air',
            self::ARGON => 'Argon',
            self::KRYPTON => 'Krypton',
            self::INCONNU => 'Inconnu'
        };
    }
}
