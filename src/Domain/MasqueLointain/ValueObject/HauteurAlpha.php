<?php

namespace App\Domain\MasqueLointain\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Hauteur d'angle de l'obstacle lointain an °
 */
final class HauteurAlpha extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur, true, null, 90);
    }
}
