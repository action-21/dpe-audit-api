<?php

namespace App\Domain\Enveloppe\Enum\Porte;

use App\Domain\Common\Enum\Enum;

enum TypeVitrage: string implements Enum
{
    case SIMPLE_VITRAGE = 'simple_vitrage';
    case DOUBLE_VITRAGE = 'double_vitrage';
    case TRIPLE_VITRAGE = 'triple_vitrage';

    public static function from_enum_type_porte_id(int $type_porte_id): ?self
    {
        return match ($type_porte_id) {
            2, 3, 6, 7, 10, 11 => self::SIMPLE_VITRAGE,
            4, 8, 12, 15 => self::DOUBLE_VITRAGE,
            1, 5, 9, 13, 14, 16 => null,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SIMPLE_VITRAGE => 'Simple vitrage',
            self::DOUBLE_VITRAGE => 'Double vitrage',
            self::TRIPLE_VITRAGE => 'Triple vitrage',
        };
    }
}
