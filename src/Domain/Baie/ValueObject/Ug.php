<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient de transmission thermique du vitrage en W/(m².K)
 */
final class Ug extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
