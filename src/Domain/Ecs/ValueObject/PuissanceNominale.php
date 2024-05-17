<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Puissance nominale du générateur d'ECS en kW
 */
final class PuissanceNominale extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
