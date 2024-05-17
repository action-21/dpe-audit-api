<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface de la baie en m²
 */
final class Surface extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
