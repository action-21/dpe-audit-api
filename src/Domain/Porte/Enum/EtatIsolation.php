<?php

namespace App\Domain\Porte\Enum;

use App\Domain\Common\Enum\Enum;

enum EtatIsolation: string implements Enum
{
    case INCONNU = 'INCONNU';
    case NON_ISOLE = 'NON_ISOLE';
    case ISOLE = 'ISOLE';

    public static function from_enum_type_porte_id(int $type_porte_id): self
    {
        return match ($type_porte_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => self::NON_ISOLE,
            13, 15 => self::ISOLE,
            14 => self::INCONNU,
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

    public function est_isole(): bool
    {
        return $this === self::ISOLE;
    }
}
