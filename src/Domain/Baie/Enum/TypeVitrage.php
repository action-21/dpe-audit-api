<?php

namespace App\Domain\Baie\Enum;

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
    case SURVITRAGE = 6;
    case SURVITRAGE_FE = 7;
    case BRIQUE_VERRE = 8;
    case POLYCARBONATE = 9;

    public static function try_from_opendata(int $enum_type_vitrage_id, ?bool $vitrage_vir): ?self
    {
        if ($vitrage_vir) {
            return match ($enum_type_vitrage_id) {
                1 => self::SIMPLE_VITRAGE,
                2 => self::DOUBLE_VITRAGE_FE,
                3 => self::TRIPLE_VITRAGE_FE,
                4 => self::SURVITRAGE_FE,
                5 => self::BRIQUE_VERRE,
                6 => self::POLYCARBONATE,
                default => null,
            };
        }
        return match ($enum_type_vitrage_id) {
            1 => self::SIMPLE_VITRAGE,
            2 => self::DOUBLE_VITRAGE,
            3 => self::TRIPLE_VITRAGE,
            4 => self::SURVITRAGE,
            5 => self::BRIQUE_VERRE,
            6 => self::POLYCARBONATE,
            default => null,
        };
    }

    /** @return array<self> */
    public static function cases_by_nature_menuiserie(NatureMenuiserie $nature_menuiserie): array
    {
        return match ($nature_menuiserie) {
            NatureMenuiserie::POLYCARBONATE => [
                self::POLYCARBONATE
            ],
            NatureMenuiserie::BRIQUE_VERRE => [
                self::BRIQUE_VERRE
            ],
            default => self::cases(),
        };
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
            self::SURVITRAGE => 'Survitrage',
            self::SURVITRAGE_FE => 'Survitrage à faible émissivité',
            self::BRIQUE_VERRE => 'Brique de Verre',
            self::POLYCARBONATE => 'Polycarbonate'
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
