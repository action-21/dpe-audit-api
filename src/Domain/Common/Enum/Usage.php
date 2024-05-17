<?php

namespace App\Domain\Common\Enum;

enum Usage: int implements Enum
{
    case CHAUFFAGE = 1;
    case ECS = 2;
    case REFROIDISSEMENT = 3;
    case ECLAIRAGE = 4;
    case BUREAUTIQUE = 5;
    case ASCENSEUR = 6;
    case AUTRES = 7;
    case PRODUCTION_ELECTRICITE = 8;
    case ABONNEMENTS = 9;
    case TRANSPORTS_MECANIQUES = 10;
    case AUXILIAIRES_VENTILATION = 16;
    case INCONNU = 12;

    /** @deprecated */
    case CHAFFAGE_ET_ECS = 11;
    /** @deprecated */
    case CHAUFFAGE_ECS_CLIMATISATION = 13;
    /** @deprecated */
    case CHAUFFAGE_CLIMATISATION = 14;
    /** @deprecated */
    case ECS_CLIMATISATION = 15;

    /**
     * @return self[]
     */
    public static function from_opendata(int $id): array
    {
        return match ($id) {
            11 => [self::CHAUFFAGE, self::ECS],
            13 => [self::CHAUFFAGE, self::ECS, self::REFROIDISSEMENT],
            14 => [self::CHAUFFAGE, self::REFROIDISSEMENT],
            15 => [self::ECS, self::REFROIDISSEMENT],
            default => [self::from($id)]
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::CHAUFFAGE => 'Chauffage',
            self::ECS => 'Eau Chaude sanitaire',
            self::REFROIDISSEMENT => 'Refroidissement',
            self::ECLAIRAGE => 'Eclairage',
            self::BUREAUTIQUE => 'Bureautique',
            self::ASCENSEUR => 'Ascenseur(s)',
            self::AUTRES => 'Autres usages',
            self::PRODUCTION_ELECTRICITE => 'Production d\'électricité à demeure',
            self::ABONNEMENTS => 'Abonnements',
            self::TRANSPORTS_MECANIQUES => 'Transports mécaniques',
            self::CHAFFAGE_ET_ECS => 'Chauffage et Eau chaude sanitaire',
            self::INCONNU => 'Périmètre de l\'usage inconnu',
            self::CHAUFFAGE_ECS_CLIMATISATION => 'Chauffage, Eau chaude sanitaire et Climatisation',
            self::CHAUFFAGE_CLIMATISATION => 'Chauffage et Climatisation',
            self::ECS_CLIMATISATION => 'Eau Chaude Sanitaire et Climatisation',
            self::AUXILIAIRES_VENTILATION => 'Auxiliaires et ventilation'
        };
    }

    /**
     * Usages couverts par le Diagnostic de Performance Energétique
     * 
     * @return self[]
     */
    public static function usages_dpe(): array
    {
        return [self::CHAUFFAGE, self::ECS, self::REFROIDISSEMENT, self::ECLAIRAGE, self::AUXILIAIRES_VENTILATION];
    }
}
