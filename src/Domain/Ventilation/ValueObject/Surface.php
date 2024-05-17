<?php

namespace App\Domain\Ventilation\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface ventilée en m²
 */
final class Surface extends Nombre
{
    public static function from(float $valeur) : self
    {
        return static::_from($valeur);
    }
}
