<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Common\ValueObject\Nombre;

/**
 * Perméabilité à l'air saisie depuis un test d'étanchéité à l'air de moins de deux ans en m3/(h.m2)
 */
final class Q4PaConv extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur);
    }
}
