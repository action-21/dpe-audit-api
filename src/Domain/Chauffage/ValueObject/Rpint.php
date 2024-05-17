<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Rendement de génération à charge intermédiaire du générateur à combustion en %
 */
final class Rpint extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
