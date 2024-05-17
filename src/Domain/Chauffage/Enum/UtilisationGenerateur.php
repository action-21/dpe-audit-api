<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum UtilisationGenerateur: int implements Enum
{
    case BASE = 1;
    case RELEVE = 2;
    case APPOINT = 3;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BASE => 'Base',
            self::RELEVE => 'Relevé',
            self::APPOINT => 'Appoint',
        };
    }
}
