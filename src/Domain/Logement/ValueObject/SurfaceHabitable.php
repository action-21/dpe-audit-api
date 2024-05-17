<?php

namespace App\Domain\Logement\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Surface habitable en m²
 */
final class SurfaceHabitable extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
