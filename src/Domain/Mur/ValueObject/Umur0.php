<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient de transmission thermique du mur non isolé en W/(m².K)
 */
final class Umur0 extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
