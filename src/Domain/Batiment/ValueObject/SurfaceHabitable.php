<?php

namespace App\Domain\Batiment\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

final class SurfaceHabitable extends Nombre
{
    public static function from(float|int $valeur): static
    {
        return static::_from($valeur);
    }
}
