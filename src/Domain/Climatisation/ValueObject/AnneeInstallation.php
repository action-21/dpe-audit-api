<?php

namespace App\Domain\Climatisation\ValueObject;

use App\Domain\Climatisation\Enum\TypeGenerateur;
use App\Domain\Common\ValueObject\Entier;

/**
 * Année d'installation de la climatisation
 */
final class AnneeInstallation extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur, min: 1900, max: \date("Y"));
    }

    public static function from_enum_periode_installation_fr_id(int $id): static
    {
        return match ($id) {
            1 => static::from(2007),
            2 => static::from(2014),
            3 => static::from(\date("Y")),
        };
    }

    public static function is_requis_by_type_generateur(TypeGenerateur $type_generateur): bool
    {
        return \in_array($type_generateur, [
            TypeGenerateur::PAC_AIR_AIR,
            TypeGenerateur::PAC_AIR_EAU,
            TypeGenerateur::PAC_EAU_EAU,
            TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
            TypeGenerateur::PAC_GEOTHERMIQUE,
        ]);
    }
}
