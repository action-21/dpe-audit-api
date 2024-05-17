<?php

namespace App\Domain\Batiment\Enum;

use App\Domain\Common\Enum\Enum;
use App\Domain\Logement\Enum\TypeLogement;

enum TypeBatiment: int implements Enum
{
    case MAISON = 1;
    case IMMEUBLE = 2;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::MAISON => 'Maison individuelle',
            self::IMMEUBLE => 'Immeuble'
        };
    }

    public function type_logement_applicable(TypeLogement $type_logement): bool
    {
        return match ($this) {
            self::MAISON => $type_logement->maison(),
            self::IMMEUBLE => $type_logement->appartement(),
        };
    }

    /**
     * Ratio du temps d'utilisation pour les ventilations hybrides (1 par défaut)
     */
    public function ratio_temps_utilisation_ventilation(): ?float
    {
        return match ($this) {
            self::MAISON => 0.083,
            //self::APPARTEMENT => 0.167,
            self::IMMEUBLE => 0.167,
            default => 1
        };
    }

    public function maison(): bool
    {
        return $this === self::MAISON;
    }

    public function immeuble(): bool
    {
        return $this === self::IMMEUBLE;
    }
}
