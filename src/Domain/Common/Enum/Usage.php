<?php

namespace App\Domain\Common\Enum;

enum Usage: string implements Enum
{
    case CHAUFFAGE = 'chauffage';
    case ECS = 'ecs';
    case REFROIDISSEMENT = 'refroidissement';
    case ECLAIRAGE = 'eclairage';
    case AUXILIAIRE = 'auxiliaire';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::CHAUFFAGE => 'Chauffage',
            self::ECS => 'Eau chaude sanitaire',
            self::REFROIDISSEMENT => 'Refroidissement',
            self::ECLAIRAGE => 'Eclairage',
            self::AUXILIAIRE => 'Auxiliaires',
        };
    }
}
