<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Proportion d'énergie solaire incidente
 */
final class Sw extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
