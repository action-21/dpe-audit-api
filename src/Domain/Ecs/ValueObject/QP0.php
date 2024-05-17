<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\Nombre;
use App\Domain\Ecs\Enum\TypeGenerateur;

/**
 * Perte à l'arrêt du générateur d'ECS en W
 */
final class QP0 extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }

    public static function is_applicable_by_type_generateur(TypeGenerateur $type_generateur): bool
    {
        return $type_generateur->generateur_combustion();
    }
}
