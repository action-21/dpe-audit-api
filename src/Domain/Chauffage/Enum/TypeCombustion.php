<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeCombustion: string implements Enum
{
    case STANDARD = 'STANDARD';
    case BASSE_TEMPERATURE = 'BASSE_TEMPERATURE';
    case CONDENSATION = 'CONDENSATION';

    public static function from_enum_type_generateur_ch_id(int $id): ?self
    {
        return match ($id) {
            50, 51, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80,
            85, 86, 87, 88, 89, 90, 109, 110, 111, 112, 113, 114, 115, 116, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128,
            129, 130, 131, 132, 140, 141, 152, 153, 154, 155, 156, 157, 158, 159, 171 => self::STANDARD,
            81, 82, 91, 92, 93, 133, 134, 135 => self::BASSE_TEMPERATURE,
            52, 83, 84, 94, 95, 96, 97, 136, 137, 138, 139, 148, 149, 150, 151, 160, 161 => self::CONDENSATION,
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
