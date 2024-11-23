<?php

namespace App\Domain\PlancherHaut\Enum;

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

    public static function from_uph(float $uph): self
    {
        return match (true) {
            $uph >= 0.3 => self::INSUFFISANTE,
            $uph >= 0.2 => self::MOYENNE,
            $uph >= 0.15 => self::BONNE,
            $uph < 0.15 => self::TRES_BONNE,
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
