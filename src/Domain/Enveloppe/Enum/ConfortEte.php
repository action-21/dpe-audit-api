<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum ConfortEte: string implements Enum
{
    case BON = 'bon';
    case MOYEN = 'moyen';
    case INSUFFISANT = 'insuffisant';

    public function id(): int|string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BON => 'Bon',
            self::MOYEN => 'Moyen',
            self::INSUFFISANT => 'Insuffisant',
        };
    }
}
