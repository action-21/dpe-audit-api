<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

enum ModeExtraction: string implements Enum
{
    case AUTOREGLABLE = 'AUTOREGLABLE';
    case HYGROREGLABLE = 'HYGROREGLABLE';
    case INCONNU = 'INCONNU';

    public static function from_enum_type_ventilation_id(int $id): ?self
    {
        return match ($id) {
            3, 4, 5, 6, 16, 25, 32, 33, 34 => self::AUTOREGLABLE,
            7, 8, 9, 10, 11, 12, 13, 14, 15, 17, 18, 29, 30, 31 => self::HYGROREGLABLE,
            19, 20, 21, 22, 23, 24, 26, 27, 28, 35, 36, 37, 38 => self::INCONNU,
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
            self::AUTOREGLABLE => 'Extraction autoréglable',
            self::HYGROREGLABLE => 'Extraction hygroréglable',
            self::INCONNU => 'Mode d\'extraction inconnu',
        };
    }
}
