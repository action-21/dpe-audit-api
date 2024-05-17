<?php

namespace App\Domain\PlancherIntermediaire\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface du plancher intermédiaire en m²
 */
final class Surface extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
