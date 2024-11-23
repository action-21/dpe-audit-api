<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

enum ModeInsufflation: string implements Enum
{
    case AUTOREGLABLE = 'AUTOREGLABLE';
    case HYGROREGLABLE = 'HYGROREGLABLE';
    case INCONNU = 'INCONNU';

    public static function from_enum_type_ventilation_id(int $id): ?self
    {
        return match ($id) {
            2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 16, 17 => self::AUTOREGLABLE,
            13, 14, 15, 18, 29, 30, 31, 34 => self::HYGROREGLABLE,
            19, 20, 21, 22, 23, 24, 26, 27, 28, 32, 33, 35, 36, 37, 38 => self::INCONNU,
            default => null
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::AUTOREGLABLE => 'Insufflation autoréglable',
            self::HYGROREGLABLE => 'Insufflation hygroréglable',
            self::INCONNU => 'Mode d\'insufflation inconnu',
        };
    }
}
