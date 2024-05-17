<?php

namespace App\Domain\Mur\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeDoublage: int implements Enum
{
    case INCONNU = 1;
    case SANS_DOUBLAGE = 2;
    case INDETERMINE = 3;
    case LAME_AIR_INFERIEUR_15MM = 4;
    case LAME_AIR_SUPERIEUR_15MM = 5;
    case MATERIAUX_CONNU = 6;

    public static function from_enum_type_doublage_id(int $id): self
    {
        return static::from($id);
    }

    public function id(): int
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

    public function resistance_doublage(): float
    {
        return match ($this) {
            self::INCONNU, self::SANS_DOUBLAGE => 0,
            self::INDETERMINE, self::LAME_AIR_INFERIEUR_15MM => 0.1,
            self::LAME_AIR_SUPERIEUR_15MM, self::MATERIAUX_CONNU => 0.21,
        };
    }
}
