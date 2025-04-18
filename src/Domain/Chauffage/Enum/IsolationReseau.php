<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum IsolationReseau: string implements Enum
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
            self::NON_ISOLE => 'Non isolé',
            self::ISOLE => 'Isolé',
        };
    }
}
