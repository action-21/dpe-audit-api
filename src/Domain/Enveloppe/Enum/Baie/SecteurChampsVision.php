<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\{Enum, Orientation as PointCardinal};
use App\Domain\Common\ValueObject\Orientation;

enum SecteurChampsVision: string implements Enum
{
    case SECTEUR_LATERAL_NORD = 'secteur_lateral_nord';
    case SECTEUR_LATERAL_EST = 'secteur_lateral_est';
    case SECTEUR_LATERAL_SUD = 'secteur_lateral_sud';
    case SECTEUR_LATERAL_OUEST = 'secteur_lateral_ouest';
    case SECTEUR_CENTRAL_NORD = 'secteur_central_nord';
    case SECTEUR_CENTRAL_EST = 'secteur_central_est';
    case SECTEUR_CENTRAL_SUD = 'secteur_central_sud';
    case SECTEUR_CENTRAL_OUEST = 'secteur_central_ouest';

    public static function determine(Orientation $baie, Orientation $masque): self
    {
        return match ($baie->enum()) {
            PointCardinal::NORD => match (true) {
                $masque->between($baie->minus(90), $baie->minus(45)) => self::SECTEUR_LATERAL_OUEST,
                $masque->between($baie->plus(45), $baie->plus(90)) => self::SECTEUR_LATERAL_EST,
                $masque->between($baie->minus(45), $baie->value()) => self::SECTEUR_CENTRAL_OUEST,
                $masque->between($baie->value(), $baie->plus(45)) => self::SECTEUR_CENTRAL_EST,
            },
            PointCardinal::SUD => match (true) {
                $masque->between($baie->plus(45), $baie->plus(90)) => self::SECTEUR_LATERAL_OUEST,
                $masque->between($baie->minus(90), $baie->minus(45)) => self::SECTEUR_LATERAL_EST,
                $masque->between($baie->value(), $baie->plus(45)) => self::SECTEUR_CENTRAL_OUEST,
                $masque->between($baie->minus(45), $baie->value()) => self::SECTEUR_CENTRAL_EST,
            },
            PointCardinal::EST => match (true) {
                $masque->between($baie->minus(90), $baie->minus(45)) => self::SECTEUR_LATERAL_NORD,
                $masque->between($baie->minus(45), $baie->value()) => self::SECTEUR_CENTRAL_NORD,
                $masque->between($baie->plus(45), $baie->plus(90)) => self::SECTEUR_LATERAL_SUD,
                $masque->between($baie->value(), $baie->plus(45)) => self::SECTEUR_CENTRAL_SUD,
            },
            PointCardinal::OUEST => match (true) {
                $masque->between($baie->plus(45), $baie->plus(90)) => self::SECTEUR_LATERAL_NORD,
                $masque->between($baie->value(), $baie->plus(45)) => self::SECTEUR_CENTRAL_NORD,
                $masque->between($baie->minus(90), $baie->minus(45)) => self::SECTEUR_LATERAL_SUD,
                $masque->between($baie->minus(45), $baie->value()) => self::SECTEUR_CENTRAL_SUD,
            },
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
}
