<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeVitrage: string implements Enum
{
    case SIMPLE_VITRAGE = 'SIMPLE_VITRAGE';
    case DOUBLE_VITRAGE = 'DOUBLE_VITRAGE';
    case DOUBLE_VITRAGE_FE = 'DOUBLE_VITRAGE_FE';
    case TRIPLE_VITRAGE = 'TRIPLE_VITRAGE';
    case TRIPLE_VITRAGE_FE = 'TRIPLE_VITRAGE_FE';

    public static function from_enum_type_vitrage_id(int $id, ?bool $vitrage_vir): ?self
    {
        return match ($id) {
            1, 4 => self::SIMPLE_VITRAGE,
            2 => $vitrage_vir ? self::DOUBLE_VITRAGE_FE : self::DOUBLE_VITRAGE,
            3 => $vitrage_vir ? self::TRIPLE_VITRAGE_FE : self::TRIPLE_VITRAGE,
            5, 6 => null,
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
            self::DOUBLE_VITRAGE_FE => 'Double vitrage à faible émissivité',
            self::TRIPLE_VITRAGE => 'Triple vitrage',
            self::TRIPLE_VITRAGE_FE => 'Triple vitrage à faible émissivité',
        };
    }

    public function est_isole(): bool
    {
        return match ($this) {
            self::TRIPLE_VITRAGE => true,
            default => false,
        };
    }

    public function epaisseur_lame_air_applicable(): bool
    {
        return $this !== self::SIMPLE_VITRAGE;
    }

    public function nature_gaz_lame_applicable(): bool
    {
        return $this !== self::SIMPLE_VITRAGE;
    }
}
