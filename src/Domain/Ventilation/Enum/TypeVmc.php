<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeVmc: string implements Enum
{
    case AUTOREGLABLE = 'AUTOREGLABLE';
    case HYGROREGLABLE_TYPE_A = 'HYGROREGLABLE_TYPE_A';
    case HYGROREGLABLE_TYPE_B = 'HYGROREGLABLE_TYPE_B';

    public static function from_enum_type_ventilation_id(int $id): ?self
    {
        return match ($id) {
            3, 4, 5, 6, 16, 26, 27, 28 => self::AUTOREGLABLE,
            7, 8, 9, 17 => self::HYGROREGLABLE_TYPE_A,
            13, 14, 15, 18, 29, 30, 31 => self::HYGROREGLABLE_TYPE_B,
            default => null,
        };
    }

    public static function from_pvent_moy(float $pvent): ?self
    {
        return match ($pvent) {
            35, 65 => self::AUTOREGLABLE,
            15, 50 => self::HYGROREGLABLE_TYPE_A,
            80, 35 => self::HYGROREGLABLE_TYPE_B,
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
            self::AUTOREGLABLE => 'Autoréglable',
            self::HYGROREGLABLE_TYPE_A => 'Hygroréglable - Type A',
            self::HYGROREGLABLE_TYPE_B => 'Hygroréglable - Type B',
        };
    }

    public static function default(): self
    {
        return self::AUTOREGLABLE;
    }
}
