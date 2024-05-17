<?php

namespace App\Domain\Refend\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Longueur du refend en contact avec une paroi déperditive en m
 */
final class Lineaire extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from($valeur);
    }
}
