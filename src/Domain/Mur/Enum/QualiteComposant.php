<?php

namespace App\Domain\Mur\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * @see Arrêté du 31 mars 2021 relatif au diagnostic de performance énergétique pour les bâtiments ou parties de bâtiments à usage d'habitation en France métropolitaine
 */
enum QualiteComposant: int implements Enum
{
    case TRES_BONNE = 1;
    case BONNE = 2;
    case MOYENNE = 3;
    case INSUFFISANTE = 4;

    public static function from_umur(float $umur): self
    {
        return match (true) {
            $umur >= 0.65 => self::INSUFFISANTE,
            $umur >= 0.45 => self::MOYENNE,
            $umur >= 0.3 => self::BONNE,
            $umur < 0.3 => self::TRES_BONNE,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::TRES_BONNE => 'Très bonne',
            self::BONNE => 'Bonne',
            self::MOYENNE => 'Moyenne',
            self::INSUFFISANTE => 'Insuffisante'
        };
    }
}
