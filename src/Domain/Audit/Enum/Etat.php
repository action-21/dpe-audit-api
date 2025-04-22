<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum Etat: string implements Enum
{
    case SIMULATION = 'simulation';
    case ANNULE = 'annulé';
    case PUBLIE = 'publié';

    public function id(): int|string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SIMULATION => 'Simulation',
            self::ANNULE => 'Annulé',
            self::PUBLIE => 'Publié',
        };
    }
}
