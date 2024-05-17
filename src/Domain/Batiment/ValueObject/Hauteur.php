<?php

namespace App\Domain\Batiment\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Hauteur sous plafond en m
 */
final class Hauteur extends Nombre
{
    public static function from(float|int $valeur): static
    {
        return static::_from($valeur);
    }
}
