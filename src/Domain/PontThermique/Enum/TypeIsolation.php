<?php

namespace App\Domain\PontThermique\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeIsolation: string implements Enum
{
    case NON_ISOLE = 'NON_ISOLE';
    case ITI = 'ITI';
    case ITE = 'ITE';
    case ITR = 'ITR';
    case ITI_ITE = 'ITI_ITE';
    case ITI_ITR = 'ITI_ITR';
    case ITE_ITR = 'ITE_ITR';
    case ITR_ITE_ITI = 'ITR_ITE_ITI';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::NON_ISOLE => 'NON ISOLE',
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
