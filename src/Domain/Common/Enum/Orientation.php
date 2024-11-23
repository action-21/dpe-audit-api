<?php

namespace App\Domain\Common\Enum;

enum Orientation: string implements Enum
{
    case NORD = 'NORD';
    case EST = 'EST';
    case SUD = 'SUD';
    case OUEST = 'OUEST';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::NORD => 'Nord',
            self::EST => 'Est',
            self::SUD => 'Sud',
            self::OUEST => 'Ouest',
        };
    }

    public static function from_enum_orientation_id(int $id): ?self
    {
        return match ($id) {
            1 => self::SUD,
            2 => self::NORD,
            3 => self::EST,
            4 => self::OUEST,
            5 => null,
        };
    }

    public static function from_azimut(float $azimut): self
    {
        return match (true) {
            $azimut <= 45, $azimut >= 315 => self::NORD,
            $azimut > 45 && $azimut < 135 => self::EST,
            $azimut >= 135 && $azimut <= 225 => self::SUD,
            $azimut > 225 && $azimut < 315 => self::OUEST,
        };
    }

    public function azimut(): float
    {
        return match ($this) {
            self::NORD => 0,
            self::EST => 90,
            self::SUD => 180,
            self::OUEST => 270,
        };
    }
}
