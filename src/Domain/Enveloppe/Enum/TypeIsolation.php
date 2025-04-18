<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeIsolation: string implements Enum
{
    case ITI = 'iti';
    case ITE = 'ite';
    case ITR = 'itr';
    case ITI_ITE = 'iti_ite';
    case ITI_ITR = 'iti_itr';
    case ITE_ITR = 'ite_itr';
    case ITR_ITE_ITI = 'itr_ite_iti';

    public static function from_enum_type_isolation_id(int $type_isolation_id): ?self
    {
        return match ($type_isolation_id) {
            3 => self::ITI,
            4 => self::ITE,
            5 => self::ITR,
            6 => self::ITI_ITE,
            7 => self::ITI_ITR,
            8 => self::ITE_ITR,
            default => null
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::ITI => 'ITI',
            self::ITE => 'ITE',
            self::ITR => 'ITR',
            self::ITI_ITE => 'ITI + ITE',
            self::ITI_ITR => 'ITI + ITR',
            self::ITE_ITR => 'ITE + ITR',
            self::ITR_ITE_ITI => 'ITR + ITE + ITI',
        };
    }
}
