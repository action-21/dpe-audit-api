<?php

namespace App\Domain\Climatisation\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Coefficient SEER (Seasonal Energy Efficiency Ratio) du système de climatisation
 */
final class Seer extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
