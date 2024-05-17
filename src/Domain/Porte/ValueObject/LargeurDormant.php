<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Largeur du dormant en mm
 */
final class LargeurDormant extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
