<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient de transmission thermique du plancher bas isolé en W/(m².K)
 */
final class Upb extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
