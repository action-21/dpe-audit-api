<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Common\Enum\Enum;

/**
 * Pérmimètre d'application
 */
enum PerimetreApplication: int implements Enum
{
    case MAISON = 1;
    case APPARTEMENT = 2;
    case IMMEUBLE = 3;

    public static function from_enum_methode_application_log_id(int $id): self
    {
        return match ($id) {
            1, 14, 18 => self::MAISON,
            6, 7, 8, 9, 17, 21, 26, 27, 28, 29, 30 => self::IMMEUBLE,
            default => self::APPARTEMENT,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::MAISON => 'Maison individuelle',
            self::APPARTEMENT => 'Appartement individuel',
            self::IMMEUBLE => 'Immeuble d\'habitation',
        };
    }

    public function type_batiment(): TypeBatiment
    {
        return match ($this) {
            self::MAISON => TypeBatiment::MAISON,
            self::APPARTEMENT => TypeBatiment::IMMEUBLE,
            self::IMMEUBLE => TypeBatiment::IMMEUBLE,
        };
    }
}
