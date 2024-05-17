<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Facteur de couverture solaire
 */
final class Fch extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
