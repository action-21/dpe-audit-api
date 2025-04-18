<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum Inertie: string implements Enum
{
    case TRES_LOURDE = 'tres_lourde';
    case LOURDE = 'lourde';
    case MOYENNE = 'moyenne';
    case LEGERE = 'legere';

    public static function from_enum_classe_inertie_id(int $id): self
    {
        return match ($id) {
            1 => self::TRES_LOURDE,
            2 => self::LOURDE,
            3 => self::MOYENNE,
            4 => self::LEGERE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
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
