<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeInstallation: int implements Enum
{
    case INDIVIDUELLE = 1;
    case COLLECTIVE = 2;
    case MIXTE = 3;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INDIVIDUELLE => 'Inidividuelle',
            self::COLLECTIVE => 'Collective',
            self::MIXTE => 'Mixte',
        };
    }
}
