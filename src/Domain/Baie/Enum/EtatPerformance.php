<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * @see Arrêté du 31 mars 2021 relatif au diagnostic de performance énergétique pour les bâtiments ou parties de bâtiments à usage d'habitation en France métropolitaine
 */
enum EtatPerformance: string implements Enum
{
    case TRES_BONNE = 'TRES_BONNE';
    case BONNE = 'BONNE';
    case MOYENNE = 'MOYENNE';
    case INSUFFISANTE = 'INSUFFISANTE';

    public static function from_ubaie(float $ubaie): self
    {
        return match (true) {
            $ubaie >= 3 => self::INSUFFISANTE,
            $ubaie >= 2.2 => self::MOYENNE,
            $ubaie >= 1.6 => self::BONNE,
            $ubaie < 1.6 => self::TRES_BONNE,
        };
    }

    public function id(): string
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
