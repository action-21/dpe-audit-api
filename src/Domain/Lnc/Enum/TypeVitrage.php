<?php

namespace App\Domain\Lnc\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * Type de vitrage
 */
enum TypeVitrage: int implements Enum
{
    case SIMPLE_VITRAGE = 1;
    case DOUBLE_VITRAGE = 2;
    case DOUBLE_VITRAGE_FE = 3;
    case TRIPLE_VITRAGE = 4;
    case TRIPLE_VITRAGE_FE = 5;

    public static function scope(): string
    {
        return 'local non chauffé . baie . type de vitrage';
    }

    public function id(): int
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
}
