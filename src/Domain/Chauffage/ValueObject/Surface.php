<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface chauffée en m²
 */
final class Surface extends Nombre
{
    public static function from(float $valeur) : self
    {
        return static::_from($valeur);
    }
}