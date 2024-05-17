<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Perimetre du plancher bas en m linéaire
 */
final class Perimetre extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
