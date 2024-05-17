<?php

namespace App\Domain\PlancherHaut\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient de transmission thermique du plancher haut non isolé en W/(m².K)
 */
final class Uph0 extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
