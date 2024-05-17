<?php

namespace App\Domain\Logement\Enum;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Common\Enum\Enum;

enum TypeLogement: int implements Enum
{
    case MAISON = 1;
    case APPARTEMENT = 2;

    public static function from_type_batiment(TypeBatiment $type_batiment): self
    {
        return match ($type_batiment) {
            TypeBatiment::MAISON => self::MAISON,
            TypeBatiment::IMMEUBLE => self::APPARTEMENT,
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
            self::APPARTEMENT => 'Appartement',
        };
    }

    public function maison(): bool
    {
        return $this === self::MAISON;
    }

    public function appartement(): bool
    {
        return $this === self::APPARTEMENT;
    }
}
