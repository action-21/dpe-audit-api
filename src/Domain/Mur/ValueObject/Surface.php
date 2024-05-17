<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface du mur en m²
 */
final class Surface extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
