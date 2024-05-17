<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum ClasseInertie: int implements Enum
{
    case TRES_LOURDE = 1;
    case LOURDE = 2;
    case MOYENNE = 3;
    case LEGERE = 4;

    public function id(): int
    {
        return $this->value;
    }

    public static function from_inertie_parois(
        bool $plancher_bas_lourd,
        bool $plancher_haut_lourd,
        bool $paroi_verticale_lourde,
    ): self {
        return match (true) {
            $plancher_bas_lourd && $plancher_haut_lourd && $paroi_verticale_lourde => self::TRES_LOURDE,
            !$plancher_bas_lourd && $plancher_haut_lourd && $paroi_verticale_lourde => self::LOURDE,
            $plancher_bas_lourd && !$plancher_haut_lourd && $paroi_verticale_lourde => self::LOURDE,
            $plancher_bas_lourd && $plancher_haut_lourd && !$paroi_verticale_lourde => self::LOURDE,
            !$plancher_bas_lourd && !$plancher_haut_lourd && $paroi_verticale_lourde => self::MOYENNE,
            !$plancher_bas_lourd && $plancher_haut_lourd && !$paroi_verticale_lourde => self::MOYENNE,
            $plancher_bas_lourd && !$plancher_haut_lourd && !$paroi_verticale_lourde => self::MOYENNE,
            !$plancher_bas_lourd && !$plancher_haut_lourd && !$paroi_verticale_lourde => self::LEGERE,
        };
    }

    public function lib(): string
    {
        return match ($this) {
            self::TRES_LOURDE => 'Très lourde',
            self::LOURDE => 'Lourde',
            self::MOYENNE => 'Moyenne',
            self::LEGERE => 'Légère',
        };
    }

    public function lourde(): bool
    {
        return match ($this) {
            self::TRES_LOURDE => true,
            self::LOURDE => true,
            default => false
        };
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
