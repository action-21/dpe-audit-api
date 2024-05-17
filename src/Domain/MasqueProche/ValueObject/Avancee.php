<?php

namespace App\Domain\MasqueProche\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Avancée du masque en m
 */
final class Avancee extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
