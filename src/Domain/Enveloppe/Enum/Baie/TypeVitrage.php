<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;
use App\Domain\Enveloppe\Enum\EtatIsolation;

enum TypeVitrage: string implements Enum
{
    case SIMPLE_VITRAGE = 'simple_vitrage';
    case DOUBLE_VITRAGE = 'double_vitrage';
    case DOUBLE_VITRAGE_FE = 'double_vitrage_fe';
    case TRIPLE_VITRAGE = 'triple_vitrage';
    case TRIPLE_VITRAGE_FE = 'triple_vitrage_fe';

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

    public function isolation(): EtatIsolation
    {
        return match ($this) {
            self::TRIPLE_VITRAGE => EtatIsolation::ISOLE,
            default => EtatIsolation::NON_ISOLE,
        };
    }

    #[\Deprecated]
    public function est_isole(): bool
    {
        return match ($this) {
            self::TRIPLE_VITRAGE => true,
            default => false,
        };
    }

    public function is_vitrage_complexe(): bool
    {
        return match ($this) {
            self::DOUBLE_VITRAGE, self::DOUBLE_VITRAGE_FE, self::TRIPLE_VITRAGE, self::TRIPLE_VITRAGE_FE => true,
            default => false,
        };
    }
}
