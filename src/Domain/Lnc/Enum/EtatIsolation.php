<?php

namespace App\Domain\Lnc\Enum;

use App\Domain\Common\Enum\Enum;

enum EtatIsolation: string implements Enum
{
    case INCONNU = 'INCONNU';
    case NON_ISOLE = 'NON_ISOLE';
    case ISOLE = 'ISOLE';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Isolation inconnue',
            self::NON_ISOLE => 'Non isolée',
            self::ISOLE => 'Isolée',
        };
    }

    public function est_isole(): bool
    {
        return $this === self::ISOLE;
    }
}
