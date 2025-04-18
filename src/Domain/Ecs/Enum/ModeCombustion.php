<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum ModeCombustion: string implements Enum
{
    case STANDARD = 'standard';
    case BASSE_TEMPERATURE = 'basse_temperature';
    case CONDENSATION = 'condensation';

    public static function from_enum_type_generateur_ecs_id(int $id): ?self
    {
        return match ($id) {
            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            45, 46, 47, 48, 49, 50, 58, 59, 60, 63, 64, 65, 66, 67, 74, 75, 76, 78, 79, 80, 81, 84, 85, 86, 87, 88,
            89, 90, 91, 92, 93, 94, 95, 96, 97, 105, 106, 107, 110, 111, 112, 113, 114, 124, 125, 126, 127, 128, 129,
            130, 131, 134 => self::STANDARD,
            41, 42, 51, 52, 53, 98, 99, 100 => self::BASSE_TEMPERATURE,
            43, 44, 54, 55, 56, 57, 61, 62, 101, 102, 103, 104, 108, 109, 120, 121, 122, 123, 132, 133 => self::CONDENSATION,
            default => null,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this->value) {
            self::STANDARD => 'Standard',
            self::BASSE_TEMPERATURE => 'Basse température',
            self::CONDENSATION => 'Condensation',
        };
    }
}
