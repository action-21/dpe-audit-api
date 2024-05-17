<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Rendement de génération à pleine charge du générateur à combustion en %
 */
final class Rpn extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
