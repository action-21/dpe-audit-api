<?php

namespace App\Domain\Logement\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Hauteur sous plafond en m
 */
final class HauteurSousPlafond extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
