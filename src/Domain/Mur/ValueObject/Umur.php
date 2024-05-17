<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient de transmission thermique du mur isolé en W/(m².K)
 */
final class Umur extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
