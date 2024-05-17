<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Epaisseur du mur non isolé en cm
 */
final class Epaisseur extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
