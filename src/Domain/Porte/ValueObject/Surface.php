<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface de la porte en m²
 */
final class Surface extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
