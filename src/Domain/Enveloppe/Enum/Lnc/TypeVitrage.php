<?php

namespace App\Domain\Enveloppe\Enum\Lnc;

use App\Domain\Common\Enum\Enum;
use App\Domain\Enveloppe\Enum\EtatIsolation;

enum TypeVitrage: string implements Enum
{
    case SIMPLE_VITRAGE = 'simple_vitrage';
    case DOUBLE_VITRAGE = 'double_vitrage';
    case DOUBLE_VITRAGE_FE = 'double_vitrage_fe';
    case TRIPLE_VITRAGE = 'triple_vitrage';
    case TRIPLE_VITRAGE_FE = 'triple_vitrage_fe';

    public static function from_tv_coef_transparence_ets_id(int $id): ?self
    {
        return match ($id) {
            1 => null,
            2, 7, 12, 17 => self::SIMPLE_VITRAGE,
            3, 8, 13, 18 => self::DOUBLE_VITRAGE,
            4, 9, 14, 19 => self::DOUBLE_VITRAGE_FE,
            5, 10, 15, 20 => self::TRIPLE_VITRAGE,
            6, 11, 16, 21 => self::TRIPLE_VITRAGE_FE,
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

    public function isolation(): EtatIsolation
    {
        return match ($this) {
            self::TRIPLE_VITRAGE, self::TRIPLE_VITRAGE_FE => EtatIsolation::ISOLE,
            default => EtatIsolation::NON_ISOLE,
        };
    }
}
