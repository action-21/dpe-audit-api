<?php

namespace App\Domain\PlancherHaut\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeIsolation: string implements Enum
{
    case INCONNU = 'INCONNU';
    case ITI = 'ITI';
    case ITE = 'ITE';
    case ITR = 'ITR';
    case ITI_ITE = 'ITI_ITE';
    case ITI_ITR = 'ITI_ITR';
    case ITE_ITR = 'ITE_ITR';
    case ITR_ITE_ITI = 'ITR_ITE_ITI';

    public static function from_enum_type_isolation_id(int $type_isolation_id): ?self
    {
        return match ($type_isolation_id) {
            1, 9 => self::INCONNU,
            2 => null,
            3 => self::ITI,
            4 => self::ITE,
            5 => self::ITR,
            6 => self::ITI_ITE,
            7 => self::ITI_ITR,
            8 => self::ITE_ITR,
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
            self::ITI => 'ITI',
            self::ITE => 'ITE',
            self::ITR => 'ITR',
            self::ITI_ITE => 'ITI + ITE',
            self::ITI_ITR => 'ITI + ITR',
            self::ITE_ITR => 'ITE + ITR',
            self::ITR_ITE_ITI => 'ITR + ITE + ITI',
        };
    }

    public function inconnu(): bool
    {
        return $this === self::INCONNU;
    }
}
