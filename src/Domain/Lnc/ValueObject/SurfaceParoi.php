<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface de la paroi exprimée en m²
 */
final class SurfaceParoi extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
