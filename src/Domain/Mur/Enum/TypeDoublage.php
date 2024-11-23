<?php

namespace App\Domain\Mur\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeDoublage: string implements Enum
{
    case INCONNU = 'INCONNU';
    case SANS_DOUBLAGE = 'SANS_DOUBLAGE';
    case INDETERMINE = 'INDETERMINE';
    case LAME_AIR_INFERIEUR_15MM = 'LAME_AIR_INFERIEUR_15MM';
    case LAME_AIR_SUPERIEUR_15MM = 'LAME_AIR_SUPERIEUR_15MM';
    case MATERIAUX_CONNU = 'MATERIAUX_CONNU';

    public static function from_enum_type_doublage_id(int $type_doublage_id): self
    {
        return match ($type_doublage_id) {
            1 => self::INCONNU,
            2 => self::SANS_DOUBLAGE,
            3 => self::LAME_AIR_INFERIEUR_15MM,
            4 => self::LAME_AIR_SUPERIEUR_15MM,
            5 => self::MATERIAUX_CONNU,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
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
            self::INCONNU, self::SANS_DOUBLAGE => 0,
            self::INDETERMINE, self::LAME_AIR_INFERIEUR_15MM => 0.1,
            self::LAME_AIR_SUPERIEUR_15MM, self::MATERIAUX_CONNU => 0.21,
        };
    }
}
