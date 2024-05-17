<?php

namespace App\Domain\PlancherIntermediaire\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Longueur du plancher intermédiaire en contact avec une paroi déperditive en m
 */
final class Lineaire extends Nombre
{
    public static function from(int|float $valeur): static
    {
        return static::_from($valeur);
    }
}
