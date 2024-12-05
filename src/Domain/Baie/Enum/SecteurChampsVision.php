<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Enum\Orientation;

enum SecteurChampsVision: string implements Enum
{
    case SECTEUR_LATERAL_NORD = 'SECTEUR_LATERAL_NORD';
    case SECTEUR_LATERAL_EST = 'SECTEUR_LATERAL_EST';
    case SECTEUR_LATERAL_SUD = 'SECTEUR_LATERAL_SUD';
    case SECTEUR_LATERAL_OUEST = 'SECTEUR_LATERAL_OUEST';
    case SECTEUR_CENTRAL_NORD = 'SECTEUR_CENTRAL_NORD';
    case SECTEUR_CENTRAL_EST = 'SECTEUR_CENTRAL_EST';
    case SECTEUR_CENTRAL_SUD = 'SECTEUR_CENTRAL_SUD';
    case SECTEUR_CENTRAL_OUEST = 'SECTEUR_CENTRAL_OUEST';

    public static function determine(float $orientation_masque, float $orientation_baie,): self
    {
        $diff = \abs($orientation_masque - $orientation_baie);

        return match (Orientation::from_azimut($orientation_baie)) {
            Orientation::NORD => match (true) {
                $diff >= -90 && $diff < -45 => self::SECTEUR_LATERAL_OUEST,
                $diff >= -45 && $diff <= 0 => self::SECTEUR_CENTRAL_OUEST,
                $diff >= 0 && $diff <= 45 => self::SECTEUR_CENTRAL_EST,
                $diff > 45 && $diff <= 90 => self::SECTEUR_LATERAL_EST,
            },
            Orientation::EST => match (true) {
                $diff >= -90 && $diff < -45 => self::SECTEUR_LATERAL_NORD,
                $diff >= -45 && $diff < 0 => self::SECTEUR_CENTRAL_NORD,
                $diff >= 0 && $diff <= 45 => self::SECTEUR_CENTRAL_SUD,
                $diff > 45 && $diff <= 90 => self::SECTEUR_LATERAL_SUD,
            },
            Orientation::SUD => match (true) {
                $diff >= -90 && $diff < -45 => self::SECTEUR_LATERAL_EST,
                $diff >= -45 && $diff <= 0 => self::SECTEUR_CENTRAL_EST,
                $diff >= 0 && $diff <= 45 => self::SECTEUR_CENTRAL_OUEST,
                $diff > 45 && $diff <= 90 => self::SECTEUR_LATERAL_OUEST,
            },
            Orientation::OUEST => match (true) {
                $diff >= -90 && $diff < -45 => self::SECTEUR_LATERAL_SUD,
                $diff >= -45 && $diff <= 0 => self::SECTEUR_CENTRAL_SUD,
                $diff > 0 && $diff <= 45 => self::SECTEUR_CENTRAL_NORD,
                $diff > 45 && $diff <= 90 => self::SECTEUR_LATERAL_NORD,
            }
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SECTEUR_LATERAL_NORD => 'Secteur latéral nord',
            self::SECTEUR_LATERAL_EST => 'Secteur latéral est',
            self::SECTEUR_LATERAL_SUD => 'Secteur latéral sud',
            self::SECTEUR_LATERAL_OUEST => 'Secteur latéral ouest',
            self::SECTEUR_CENTRAL_NORD => 'Secteur central nord',
            self::SECTEUR_CENTRAL_EST => 'Secteur central est',
            self::SECTEUR_CENTRAL_SUD => 'Secteur central sud',
            self::SECTEUR_CENTRAL_OUEST => 'Secteur central ouest',
        };
    }

    /** @return self[] */
    public static function secteurs_by_orientation(Orientation $orientation_baie): array
    {
        return match ($orientation_baie) {
            Orientation::NORD => [
                self::SECTEUR_LATERAL_OUEST,
                self::SECTEUR_CENTRAL_OUEST,
                self::SECTEUR_CENTRAL_EST,
                self::SECTEUR_LATERAL_EST,
            ],
            Orientation::EST => [
                self::SECTEUR_LATERAL_NORD,
                self::SECTEUR_CENTRAL_NORD,
                self::SECTEUR_CENTRAL_SUD,
                self::SECTEUR_LATERAL_SUD,
            ],
            Orientation::SUD => [
                self::SECTEUR_LATERAL_EST,
                self::SECTEUR_CENTRAL_EST,
                self::SECTEUR_CENTRAL_OUEST,
                self::SECTEUR_LATERAL_OUEST,
            ],
            Orientation::OUEST => [
                self::SECTEUR_LATERAL_SUD,
                self::SECTEUR_CENTRAL_SUD,
                self::SECTEUR_CENTRAL_NORD,
                self::SECTEUR_LATERAL_NORD,
            ],
        };
    }
}
