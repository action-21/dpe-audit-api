<?php

namespace App\Domain\Visite\Enum;

use App\Domain\Common\Enum\Enum;

enum Typologie: string implements Enum
{
    case T1 = 'T1';
    case T2 = 'T2';
    case T3 = 'T3';
    case T4 = 'T4';
    case T5 = 'T5';
    case T6 = 'T6';
    case T7 = 'T7';

    public static function from_enum_typologie_logement_id(int $id): self
    {
        return match ($id) {
            1 => self::T1,
            2 => self::T2,
            3 => self::T3,
            4 => self::T4,
            5 => self::T5,
            6 => self::T6,
            7 => self::T7,
        };
    }

    public static function from_surface_habitable(float $surface_habitable): self
    {
        return match (true) {
            $surface_habitable <= 30 => self::T1,
            $surface_habitable <= 50 => self::T2,
            $surface_habitable <= 70 => self::T3,
            $surface_habitable <= 90 => self::T4,
            $surface_habitable <= 110 => self::T5,
            $surface_habitable <= 130 => self::T6,
            default => self::T7,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this->value) {
            self::T1 => 'T1',
            self::T2 => 'T2',
            self::T3 => 'T3',
            self::T4 => 'T4',
            self::T5 => 'T5',
            self::T6 => 'T6',
            self::T7 => 'T7 ou plus',
        };
    }
}
