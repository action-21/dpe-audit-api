<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum ClasseAltitude: string implements Enum
{
    case _400_LT = '400_lt';
    case _400_800 = '400_800';
    case _800_GT = '800_gt';

    public static function from_classe_altitude_id(int $id): self
    {
        return match ($id) {
            1 => self::_400_LT,
            2 => self::_400_800,
            3 => self::_800_GT,
        };
    }

    public static function from_classe_altitude(string $value): self
    {
        return match ($value) {
            'inférieur à 400m' => self::_400_LT,
            '400-800m' => self::_400_800,
            'supérieur à 800m' => self::_800_GT,
        };
    }

    public function id(): int|string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::_400_LT => 'Inférieure à 400m',
            self::_400_800 => '400-800m',
            self::_800_GT => 'Supérieure à 800m',
        };
    }

    public function filter(): string
    {
        return match ($this) {
            self::_400_LT => 'inférieur à 400m',
            self::_400_800 => '400-800m',
            self::_800_GT => 'supérieur à 800m',
        };
    }

    public function floatval(): float
    {
        return match ($this) {
            self::_400_LT => 200,
            self::_400_800 => 600,
            self::_800_GT => 1000,
        };
    }
}
