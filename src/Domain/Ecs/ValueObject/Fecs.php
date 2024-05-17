<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Facteur de couverture solaire
 */
final class Fecs extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
