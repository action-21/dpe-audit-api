<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum IsolationReseau: string implements Enum
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
            self::INCONNU => 'Inconnu',
            self::NON_ISOLE => 'Non isolé',
            self::ISOLE => 'Isolé',
        };
    }
}
