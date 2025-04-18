<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;

enum TypeSurvitrage: string implements Enum
{
    case SURVITRAGE_SIMPLE = 'survitrage_simple';
    case SURVITRAGE_FE = 'survitrage_fe';

    public static function from_enum_type_vitrage_id(int $id, ?bool $vitrage_vir): ?self
    {
        return match ($id) {
            4 => $vitrage_vir ? self::SURVITRAGE_FE : self::SURVITRAGE_SIMPLE,
            1, 2, 3, 5, 6 => null,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SURVITRAGE_SIMPLE => 'Survitrage simple',
            self::SURVITRAGE_FE => 'Survitrage à faible émissivité',
        };
    }
}
