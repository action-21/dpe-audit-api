<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient de transmission thermique (vitrage + menuiserie) (W/(m².K))
 */
final class Uw extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
