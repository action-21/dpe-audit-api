<?php

namespace App\Domain\Porte\Enum;

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

    public static function from_uporte(float $value): self
    {
        return match (true) {
            $value >= 3 => self::INSUFFISANTE,
            $value >= 2.2 => self::MOYENNE,
            $value >= 1.6 => self::BONNE,
            $value < 1.6 => self::TRES_BONNE,
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
