<?php

namespace App\Domain\PontThermique\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Valeur du pont thermique en W/(m.K)
 */
final class Kpt extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
