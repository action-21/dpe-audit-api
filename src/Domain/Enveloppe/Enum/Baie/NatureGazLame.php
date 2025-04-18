<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;

enum NatureGazLame: string implements Enum
{
    case AIR = 'air';
    case ARGON = 'argon';
    case KRYPTON = 'krypton';

    public static function from_enum_type_gaz_lame_id(int $id): ?self
    {
        return match ($id) {
            1 => self::AIR,
            2 => self::ARGON,
            default => null,
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
        };
    }
}
