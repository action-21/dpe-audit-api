<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Puissance de la veilleuse du générateur à combustion en W
 */
final class PuissanceVeilleuse extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
