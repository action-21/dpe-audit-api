<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Common\Enum\Enum;

/**
 * Méthodes d'application de l'audit
 */
enum MethodeApplication: int implements Enum
{
    case MAISON = 1;
    case APPARTEMENT = 2;
    case IMMEUBLE = 3;
    case APPARTEMENT_DEPUIS_IMMEUBLE = 4;
    case RT2012_MAISON = 5;
    case RT2012_APPARTEMENT = 6;
    case RT2012_IMMEUBLE = 7;
    case RE2020_MAISON = 8;
    case RT2020_APPARTEMENT = 9;
    case RE2020_IMMEUBLE = 10;

    public static function from_enum_methode_application_dpe_log_id(int $id): self
    {
        return match ($id) {
            1 => self::MAISON,
            2, 3, 4, 5, 31, 32, 35, 36, 37 => self::APPARTEMENT,
            6, 7, 8, 9, 26, 27, 28, 29, 30 => self::IMMEUBLE,
            10, 11, 12, 13, 33, 34, 38, 39, 40 => self::APPARTEMENT_DEPUIS_IMMEUBLE,
            14 => self::RT2012_MAISON,
            15, 16, 22, 23 => self::RT2012_APPARTEMENT,
            17 => self::RT2012_IMMEUBLE,
            18 => self::RE2020_MAISON,
            19, 20, 24, 25 => self::RT2020_APPARTEMENT,
            21 => self::RE2020_IMMEUBLE,
            default => throw new \InvalidArgumentException("Unknown id $id for " . self::class),
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::MAISON => 'Audit - Maison individuelle',
            self::APPARTEMENT => 'Audit - Appartement individuel',
            self::IMMEUBLE => 'Audit - Immeuble collectif',
            self::APPARTEMENT_DEPUIS_IMMEUBLE => 'Audit - Appartement généré à partir de l\'immeuble',
            self::RT2012_MAISON => 'RT2012 - Maison individuelle',
            self::RT2012_APPARTEMENT => 'RT2012 - Appartement individuel',
            self::RT2012_IMMEUBLE => 'RT2012 - Immeuble collectif',
            self::RE2020_MAISON => 'RE2020 - Maison individuelle',
            self::RT2020_APPARTEMENT => 'RT2020 - Appartement individuel',
            self::RE2020_IMMEUBLE => 'RE2020 - Immeuble collectif',
        };
    }

    public function modele(): MethodeCalcul
    {
        return match ($this) {
            self::MAISON, self::APPARTEMENT, self::IMMEUBLE, self::APPARTEMENT_DEPUIS_IMMEUBLE => MethodeCalcul::_3CLDPE2021,
            self::RT2012_MAISON, self::RT2012_APPARTEMENT, self::RT2012_IMMEUBLE => MethodeCalcul::RT2012,
            self::RE2020_MAISON, self::RT2020_APPARTEMENT, self::RE2020_IMMEUBLE => MethodeCalcul::RE2020,
        };
    }

    public function perimetre_application(): PerimetreApplication
    {
        return match ($this) {
            self::MAISON, self::RT2012_MAISON, self::RE2020_MAISON => PerimetreApplication::MAISON,
            self::APPARTEMENT, self::APPARTEMENT_DEPUIS_IMMEUBLE, self::RT2012_APPARTEMENT, self::RT2020_APPARTEMENT => PerimetreApplication::APPARTEMENT,
            self::IMMEUBLE, self::RT2012_IMMEUBLE, self::RE2020_IMMEUBLE => PerimetreApplication::IMMEUBLE,
        };
    }

    public function type_batiment(): TypeBatiment
    {
        return match ($this) {
            self::MAISON, self::RT2012_MAISON, self::RE2020_MAISON => TypeBatiment::MAISON,
            default => TypeBatiment::IMMEUBLE,
        };
    }

    /*
    public function surface_reference(): SurfaceReference
    {
        return match ($this) {
            self::MAISON => SurfaceReference::LOGEMENT,
            self::APPARTEMENT => SurfaceReference::LOGEMENT,
            self::IMMEUBLE => SurfaceReference::IMMEUBLE,
            self::APPARTEMENT_DEPUIS_IMMEUBLE => SurfaceReference::IMMEUBLE,
            self::RT2012_MAISON => SurfaceReference::LOGEMENT,
            self::RT2012_APPARTEMENT => SurfaceReference::LOGEMENT,
            self::RT2012_IMMEUBLE => SurfaceReference::IMMEUBLE,
            self::RE2020_MAISON => SurfaceReference::LOGEMENT,
            self::RT2020_APPARTEMENT => SurfaceReference::LOGEMENT,
            self::RE2020_IMMEUBLE => SurfaceReference::IMMEUBLE,
        };
    }
    */
}
