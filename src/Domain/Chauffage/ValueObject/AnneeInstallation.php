<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Chauffage\Enum\TypeDistribution;
use App\Domain\Common\ValueObject\Entier;

/**
 * Année d'installation
 */
final class AnneeInstallation extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur, min: 1900, max: \date("Y"));
    }

    public static function from_enum_periode_installation_emetteur_id(int $id): self
    {
        return match ($id) {
            1 => static::from(1980),
            2 => static::from(2000),
            3 => static::from(\date("Y")),
        };
    }

    public static function try_from_enum_type_generateur_ch_id(int $id): ?self
    {
        return match ($id) {
            75 => static::from(1969),
            76 => static::from(1975),
            55, 62, 69, 120 => static::from(1977),
            77, 85, 127 => static::from(1980),
            86, 94, 128, 136 => static::from(1985),
            20, 21, 22, 23 => static::from(1989),
            78, 87, 129 => static::from(1990),
            56, 63, 70, 121 => static::from(1994),
            88, 91, 95, 130, 133, 137 => static::from(2000),
            57, 64, 71, 122 => static::from(2003),
            24, 25, 26, 27 => static::from(2004),
            50, 53 => static::from(2005),
            32, 33, 34, 35 => static::from(2006),
            1, 4, 8, 12, 16 => static::from(2007),
            44, 48, 140 => static::from(2011),
            58, 65, 72, 123 => static::from(2012),
            2, 5, 9, 13, 17, 145, 162, 165, 168 => static::from(2014),
            79, 81, 83, 89, 92, 96, 131, 134, 138, 148, 150, 160 => static::from(2015),
            6, 10, 14, 18, 146, 163, 166, 169 => static::from(2016),
            36, 37, 38, 39, 59, 66, 124, 154, 157 => static::from(2017),
            45, 60, 67, 73, 125, 152, 155, 158 => static::from(2019),
            3, 7, 11, 15, 19, 28, 29, 30, 31, 40, 41, 42, 43, 46, 49, 51, 52, 54, 61, 68, 74, 80, 82, 84, 90, 93, 97, 126, 132, 135, 139, 141, 147, 149, 151, 153, 156, 159, 161, 164, 167, 170 => static::from(\date("Y")),
            default => null,
        };
    }

    public static function is_applicable_by_type_distribution(TypeDistribution $type_distribution): bool
    {
        return match ($type_distribution) {
            TypeDistribution::SANS => false,
            default => true,
        };
    }
}
