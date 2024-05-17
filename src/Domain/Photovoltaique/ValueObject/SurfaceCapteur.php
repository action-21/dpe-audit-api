<?php

namespace App\Domain\Photovoltaique\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface du capteur en m²
 */
final class SurfaceCapteur extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
