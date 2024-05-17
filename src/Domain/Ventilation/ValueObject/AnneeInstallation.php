<?php

namespace App\Domain\Ventilation\ValueObject;

use App\Domain\Common\ValueObject\Entier;
use App\Domain\Ventilation\Enum\TypeVentilation;

/**
 * Année d'installation de la ventilation
 */
final class AnneeInstallation extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur, min: 1900, max: \date("Y"));
    }

    public static function try_from_enum_type_installation_id(int $id): ?self
    {
        return match ($id) {
            3 => static::from(1981),
            4, 7, 10, 13, 26, 29 => static::from(2000),
            5, 8, 11, 14, 19, 21, 23, 27, 30, 32, 35, 37 => static::from(2012),
            1, 2, 25, 34 => null,
            default => static::from(\date("Y")),
        };
    }

    public static function is_applicable_by_type_ventilation(TypeVentilation $type_ventilation): bool
    {
        return $type_ventilation->ventilation_mecanique();
    }
}
