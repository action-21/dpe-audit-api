<?php

namespace App\Domain\Common\Enum;

use App\Domain\Common\Enum\Enum;

enum Mois: int implements Enum
{
    case JANVIER = 1;
    case FEVRIER = 2;
    case MARS = 3;
    case AVRIL = 4;
    case MAI = 5;
    case JUIN = 6;
    case JUILLET = 7;
    case AOUT = 8;
    case SEPTEMBRE = 9;
    case OCTOBRE = 10;
    case NOVEMBRE = 11;
    case DECEMBRE = 12;

    /**
     * Nombre de jours dans l'année
     */
    public final const NOMBRE_JOURS = 365;

    /**
     * Nombre de jours d'occupation sur l'année
     */
    public final const NOMBRE_JOURS_OCCUPATION = 358;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::JANVIER => 'Janvier',
            self::FEVRIER => 'Février',
            self::MARS => 'Mars',
            self::AVRIL => 'Avril',
            self::MAI => 'Mai',
            self::JUIN => 'Juin',
            self::JUILLET => 'Juillet',
            self::AOUT => 'Août',
            self::SEPTEMBRE => 'Septembre',
            self::OCTOBRE => 'Octobre',
            self::NOVEMBRE => 'Novembre',
            self::DECEMBRE => 'Décembre',
        };
    }

    public static function from_iso(string $mois): self
    {
        return match ($mois) {
            '01' => self::JANVIER,
            '02' => self::FEVRIER,
            '03' => self::MARS,
            '04' => self::AVRIL,
            '05' => self::MAI,
            '06' => self::JUIN,
            '07' => self::JUILLET,
            '08' => self::AOUT,
            '09' => self::SEPTEMBRE,
            '10' => self::OCTOBRE,
            '11' => self::NOVEMBRE,
            '12' => self::DECEMBRE,
        };
    }

    public function to_iso(): string
    {
        return match ($this) {
            self::JANVIER => '01',
            self::FEVRIER => '02',
            self::MARS => '03',
            self::AVRIL => '04',
            self::MAI => '05',
            self::JUIN => '06',
            self::JUILLET => '07',
            self::AOUT => '08',
            self::SEPTEMBRE => '09',
            self::OCTOBRE => '10',
            self::NOVEMBRE => '11',
            self::DECEMBRE => '12',
        };
    }

    /**
     * Nombre de jours dans le mois j
     */
    public function jours(): int
    {
        return match ($this) {
            self::JANVIER => 31,
            self::FEVRIER => 28,
            self::MARS => 31,
            self::AVRIL => 30,
            self::MAI => 31,
            self::JUIN => 30,
            self::JUILLET => 31,
            self::AOUT => 31,
            self::SEPTEMBRE => 30,
            self::OCTOBRE => 31,
            self::NOVEMBRE => 30,
            self::DECEMBRE => 31,
        };
    }

    /**
     * Nj,j - Nombre de jours d'occupation sur le mois j
     */
    public function jours_occupation(): int
    {
        return match ($this) {
            self::JANVIER => 31,
            self::FEVRIER => 28,
            self::MARS => 31,
            self::AVRIL => 30,
            self::MAI => 31,
            self::JUIN => 30,
            self::JUILLET => 31,
            self::AOUT => 31,
            self::SEPTEMBRE => 30,
            self::OCTOBRE => 31,
            self::NOVEMBRE => 30,
            self::DECEMBRE => 24,
        };
    }
}
