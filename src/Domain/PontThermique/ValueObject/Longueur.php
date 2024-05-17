<?php

namespace App\Domain\PontThermique\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Longueur du pont thermique en m
 */
final class Longueur extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
