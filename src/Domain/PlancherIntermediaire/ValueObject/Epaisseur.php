<?php

namespace App\Domain\PlancherIntermediaire\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Epaisseur moyenne du plancher intermédiaire en cm
 */
final class Epaisseur extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from($valeur);
    }
}
