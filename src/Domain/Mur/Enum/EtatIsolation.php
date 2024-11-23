<?php

namespace App\Domain\Mur\Enum;

use App\Domain\Common\Enum\Enum;

enum EtatIsolation: string implements Enum
{
    case INCONNU = 'INCONNU';
    case NON_ISOLE = 'NON_ISOLE';
    case ISOLE = 'ISOLE';

    public static function from_enum_type_isolation_id(int $type_isolation_id): self
    {
        return match ($type_isolation_id) {
            1, 9 => self::INCONNU,
            2 => self::NON_ISOLE,
            3, 4, 5, 6, 7, 8 => self::ISOLE,
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
            self::NON_ISOLE => 'Non isolé',
            self::ISOLE => 'Isolé',
        };
    }
}
