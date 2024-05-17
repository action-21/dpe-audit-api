<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Volume de stockage en litres
 */
final class VolumeStockage extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
