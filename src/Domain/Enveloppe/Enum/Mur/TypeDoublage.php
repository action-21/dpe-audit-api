<?php

namespace App\Domain\Enveloppe\Enum\Mur;

use App\Domain\Common\Enum\Enum;

enum TypeDoublage: string implements Enum
{
    case SANS_DOUBLAGE = 'sans_doublage';
    case INDETERMINE = 'indetermine';
    case LAME_AIR_INFERIEUR_15MM = 'lame_air_inferieur_15mm';
    case LAME_AIR_SUPERIEUR_15MM = 'lame_air_superieur_15mm';
    case MATERIAUX_CONNU = 'materiaux_connu';

    public static function from_enum_type_doublage_id(int $type_doublage_id): ?self
    {
        return match ($type_doublage_id) {
            2 => self::SANS_DOUBLAGE,
            3 => self::LAME_AIR_INFERIEUR_15MM,
            4 => self::LAME_AIR_SUPERIEUR_15MM,
            5 => self::MATERIAUX_CONNU,
            default => null,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SANS_DOUBLAGE => 'Absence de doublage',
            self::INDETERMINE => 'Doublage rapporté de nature indeterminée',
            self::LAME_AIR_INFERIEUR_15MM => 'Doublage rapporté avec une lame d\'air de moins de 15mm',
            self::LAME_AIR_SUPERIEUR_15MM => 'Doublage rapporté avec une lame d\'air de plus de 15mm',
            self::MATERIAUX_CONNU => 'Doublage connu (plâtre brique bois)'
        };
    }

    public function resistance_thermique_doublage(): float
    {
        return match ($this) {
            self::SANS_DOUBLAGE => 0,
            self::INDETERMINE, self::LAME_AIR_INFERIEUR_15MM => 0.1,
            self::LAME_AIR_SUPERIEUR_15MM, self::MATERIAUX_CONNU => 0.21,
        };
    }
}
