<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeEmission: string implements Enum
{
    case AIR_SOUFFLE = 'AIR_SOUFFLE';
    case PLANCHER_CHAUFFANT = 'PLANCHER_CHAUFFANT';
    case PLAFOND_CHAUFFANT = 'PLAFOND_CHAUFFANT';
    case RADIATEUR = 'RADIATEUR';

    public static function from_type_emetteur(TypeEmetteur $type_emetteur): self
    {
        return match ($type_emetteur) {
            TypeEmetteur::PLANCHER_CHAUFFANT => self::PLANCHER_CHAUFFANT,
            TypeEmetteur::PLAFOND_CHAUFFANT => self::PLAFOND_CHAUFFANT,
            TypeEmetteur::RADIATEUR_MONOTUBE,
            TypeEmetteur::RADIATEUR_BITUBE,
            TypeEmetteur::RADIATEUR => self::RADIATEUR,
        };
    }

    public static function from_type_generateur(TypeGenerateur $type_generateur): self
    {
        return match ($type_generateur) {
            TypeGenerateur::GENERATEUR_AIR_CHAUD,
            TypeGenerateur::GENERATEUR_AIR_CHAUD,
            TypeGenerateur::PAC_AIR_AIR => self::AIR_SOUFFLE,
            default => self::RADIATEUR,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::AIR_SOUFFLE => 'Air soufflé',
            self::PLANCHER_CHAUFFANT => 'Plancher chauffant',
            self::PLAFOND_CHAUFFANT => 'Plafond chauffant',
            self::RADIATEUR => 'Radiateur',
        };
    }
}
