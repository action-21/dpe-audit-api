<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * TODO: Vérifier les autres types d'émetteurs (cf 15.2.1)
 */
enum TypeEmetteur: string implements Enum
{
    case PLANCHER_CHAUFFANT = 'PLANCHER_CHAUFFANT';
    case PLAFOND_CHAUFFANT = 'PLAFOND_CHAUFFANT';
    case RADIATEUR_MONOTUBE = 'RADIATEUR_MONOTUBE';
    case RADIATEUR_BITUBE = 'RADIATEUR_BITUBE';
    case RADIATEUR = 'RADIATEUR';

    public static function from_type_emission_distribution_id(int $id): ?self
    {
        return match ($id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 19, 20, 21, 22, 23, 40, 42, 46, 47, 48, 49, 50 => null,
            11, 12, 13, 14, 43 => self::PLANCHER_CHAUFFANT,
            15, 16, 17, 18, 44 => self::PLAFOND_CHAUFFANT,
            24, 25, 26, 27, 28, 29, 30, 31 => self::RADIATEUR_MONOTUBE,
            32, 33, 34, 35, 36, 37, 38, 39 => self::RADIATEUR_BITUBE,
            41, 45 => self::RADIATEUR,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::PLANCHER_CHAUFFANT => 'Plancher chauffant',
            self::PLAFOND_CHAUFFANT => 'Plafond chauffant',
            self::RADIATEUR_MONOTUBE => 'Radiateur monotube',
            self::RADIATEUR_BITUBE => 'Radiateur bitube',
            self::RADIATEUR => 'Autre radiateur',
        };
    }

    /**
     * Perte de charge de l'émetteur en kPa
     */
    public function perte_charge(): float
    {
        return match ($this) {
            self::PLANCHER_CHAUFFANT => 15,
            self::PLAFOND_CHAUFFANT => 15,
            self::RADIATEUR_MONOTUBE => 30,
            self::RADIATEUR_BITUBE => 10,
            self::RADIATEUR => 10,
        };
    }

    public function fcot(): float
    {
        return match ($this) {
            self::PLANCHER_CHAUFFANT => 0.156,
            default => 0.802,
        };
    }
}
