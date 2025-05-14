<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum PositionLogement: string implements Enum
{
    case RDC = 'rdc';
    case ETAGE_INTERMEDIAIRE = 'etage_intermediaire';
    case DERNIER_ETAGE = 'dernier_etage';

    public static function from_enum_position_etage_logement_id(int $id): self
    {
        return match ($id) {
            1 => self::RDC,
            2 => self::ETAGE_INTERMEDIAIRE,
            3 => self::DERNIER_ETAGE,
        };
    }

    public function id(): int|string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::RDC => 'Rez-de-chaussée',
            self::ETAGE_INTERMEDIAIRE => 'Étage intermédiaire',
            self::DERNIER_ETAGE => 'Dernier étage',
        };
    }
}
