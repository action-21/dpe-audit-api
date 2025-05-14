<?php

namespace App\Domain\Refroidissement\Enum;

use App\Domain\Common\Enum\Energie;

enum EnergieGenerateur: string
{
    case ELECTRICITE = 'electricite';
    case GAZ_NATUREL = 'gaz_naturel';
    case GPL = 'gpl';
    case RESEAU_FROID = 'reseau_froid';

    public static function from_enum_type_energie_id(int $id): static
    {
        return match ($id) {
            1, 12 => self::ELECTRICITE,
            2 => self::GAZ_NATUREL,
            9, 10, 13 => self::GPL,
            15 => self::RESEAU_FROID,
        };
    }

    public static function from_enum_type_generateur_fr_id(int $id): static
    {
        return match ($id) {
            21 => self::GAZ_NATUREL,
            23 => self::RESEAU_FROID,
            default => self::ELECTRICITE,
        };
    }

    public function to(): Energie
    {
        return Energie::from($this->value);
    }
}
