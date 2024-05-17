<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient de transmission thermique en W/(m².K)
 */
final class Uporte extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
