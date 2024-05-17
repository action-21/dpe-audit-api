<?php

namespace App\Domain\Logement\Enum;

use App\Domain\Common\Enum\Enum;

enum PositionEtage: int implements Enum
{
    case REZ_DE_CHAUSSEE = 1;
    case ETAGE_INTERMEDIAIRE = 2;
    case DERNIER_ETAGE = 3;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::REZ_DE_CHAUSSEE => 'Rez-de-chaussée',
            self::ETAGE_INTERMEDIAIRE => 'Etage intermédiaire',
            self::DERNIER_ETAGE => 'Dernier étage',
        };
    }
}
