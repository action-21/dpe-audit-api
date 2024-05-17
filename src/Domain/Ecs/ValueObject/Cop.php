<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\Nombre;
use App\Domain\Ecs\Enum\TypeGenerateur;

/**
 * Coefficent de performance du générateur d'ECS
 */
final class Cop extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }

    public static function is_applicable_by_type_generateur(TypeGenerateur $type_generateur): bool
    {
        return \in_array($type_generateur, [
            TypeGenerateur::CET_SUR_AIR_AMBIANT,
            TypeGenerateur::CET_SUR_AIR_EXTERIEUR,
            TypeGenerateur::CET_SUR_AIR_EXTRAIT,
            TypeGenerateur::PAC_DOUBLE_SERVICE,
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
        ]);
    }
}
