<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum Inertie: string implements Enum
{
    case INCONNUE = 'INCONNUE';
    case TRES_LOURDE = 'TRES_LOURDE';
    case LOURDE = 'LOURDE';
    case MOYENNE = 'MOYENNE';
    case LEGERE = 'LEGERE';

    public static function from_inertie_parois(
        bool $inertie_planchers_bas,
        bool $inertie_planchers_hauts,
        bool $inertie_parois_verticales,
    ): self {
        $counter = (int) $inertie_planchers_bas + (int) $inertie_planchers_hauts + (int) $inertie_parois_verticales;

        return match ($counter) {
            3 => self::TRES_LOURDE,
            2 => self::LOURDE,
            1 => self::MOYENNE,
            0 => self::LEGERE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNUE => 'Inertie inconnue',
            self::TRES_LOURDE => 'Inertie très lourde',
            self::LOURDE => 'Inertie lourde',
            self::MOYENNE => 'Inertie moyenne',
            self::LEGERE => 'Inertie légère',
        };
    }

    public function est_lourd(): bool
    {
        return $this->value === self::TRES_LOURDE || $this->value === self::LOURDE;
    }

    /**
     * Exposant utilisé pour le calcul des apports gratuits
     */
    public function exposant(): float
    {
        return match ($this) {
            self::TRES_LOURDE, self::LOURDE => 3.6,
            self::MOYENNE => 2.9,
            self::LEGERE => 2.5,
        };
    }

    /**
     * Cin - Capacité thermique intérieure efficace de la zone (J/K)
     */
    public function cin(): float
    {
        return match ($this) {
            self::TRES_LOURDE, self::LOURDE => 260000,
            self::MOYENNE => 165000,
            self::LEGERE => 110000,
        };
    }
}
