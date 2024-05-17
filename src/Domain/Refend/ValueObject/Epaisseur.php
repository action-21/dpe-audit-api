<?php

namespace App\Domain\Refend\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Epaisseur moyenne du refend en cm
 */
final class Epaisseur extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from($valeur);
    }
}
