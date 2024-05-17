<?php

namespace App\Domain\Common\Enum;

enum Orientation: int implements Enum
{
    case N = 0;
    case NE = 45;
    case E = 90;
    case SE = 135;
    case S = 180;
    case SO = 225;
    case O = 270;
    case NO = 315;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::N => 'Nord',
            self::NE => 'Nord-Est',
            self::NO => 'Nord-Ouest',
            self::E => 'Est',
            self::SE => 'Sud-Est',
            self::S => 'Sud',
            self::SO => 'Sud-Ouest',
            self::O => 'Ouest',
        };
    }

    public static function try_from_enum_orientation_id(int $id): ?self
    {
        return match ($id) {
            1 => self::S,
            2 => self::N,
            3 => self::E,
            4 => self::O,
            default => null,
        };
    }

    public static function from_enum_orientation_id(int $id): self
    {
        return match ($id) {
            1 => self::S,
            2 => self::N,
            3 => self::E,
            4 => self::O,
        };
    }

    public static function from_enum_orientation_pv_id(int $id): self
    {
        return match ($id) {
            1 => self::E,
            2 => self::SE,
            3 => self::S,
            4 => self::SO,
            5 => self::O,
        };
    }

    public static function from_azimut(float $azimut): self
    {
        return match (true) {
            $azimut <= 22.5, $azimut >= 337.5 => self::N,
            $azimut > 22.5 && $azimut <= 67.5 => self::NE,
            $azimut > 67.5 && $azimut < 112.5 => self::E,
            $azimut >= 112.5 && $azimut < 157.5 => self::SE,
            $azimut >= 157.5 && $azimut <= 202.5 => self::S,
            $azimut > 202.5 && $azimut <= 247.5 => self::SO,
            $azimut > 247.5 && $azimut < 292.5 => self::O,
            $azimut >= 292.5 && $azimut < 337.5 => self::NO,
        };
    }

    public static function from_code(string $code): self
    {
        return match ($code) {
            'N' => self::N,
            'NE' => self::NE,
            'NO' => self::NO,
            'E' => self::E,
            'SE' => self::SE,
            'S' => self::S,
            'SO' => self::SO,
            'O' => self::O,
        };
    }

    public function code(): string
    {
        return match ($this) {
            self::N => 'N',
            self::NE => 'NE',
            self::NO => 'NO',
            self::E => 'E',
            self::SE => 'SE',
            self::S => 'S',
            self::SO => 'SO',
            self::O => 'O',
        };
    }

    public function point_cardinal(): string
    {
        return match ($this) {
            self::N => 'N',
            self::NE => 'N',
            self::NO => 'N',
            self::E => 'E',
            self::SE => 'S',
            self::S => 'S',
            self::SO => 'S',
            self::O => 'O',
        };
    }

    public function to_azimut(): float
    {
        return (float) $this->value;
    }
}
