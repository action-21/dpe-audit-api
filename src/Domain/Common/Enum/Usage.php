<?php

namespace App\Domain\Common\Enum;

enum Usage: string implements Enum
{
    case CHAUFFAGE = 'CHAUFFAGE';
    case ECS = 'ECS';
    case REFROIDISSEMENT = 'REFROIDISSEMENT';
    case ECLAIRAGE = 'ECLAIRAGE';
    case AUXILIAIRE = 'AUXILIAIRE';
    case ENSEMBLE = 'ENSEMBLE';

    /**
     * @return self[]
     */
    public static function usages(): array
    {
        return [
            self::CHAUFFAGE,
            self::ECS,
            self::REFROIDISSEMENT,
            self::ECLAIRAGE,
            self::AUXILIAIRE,
        ];
    }

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
            self::ENSEMBLE => '5 usages',
        };
    }
}
