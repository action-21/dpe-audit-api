<?php

namespace App\Domain\Lnc\Enum;

use App\Domain\Common\Enum\Enum;

enum EtatIsolation: string implements Enum
{
    case NON_ISOLE = 'non_isole';
    case ISOLE = 'isole';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::NON_ISOLE => 'Non isolée',
            self::ISOLE => 'Isolée',
        };
    }

    public function boolval(): bool
    {
        return $this === self::ISOLE;
    }

    public function est_isole(): bool
    {
        return $this === self::ISOLE;
    }
}
