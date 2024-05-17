<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\ValueObject\Entier;

/**
 * Orde de priorité du générateur en cascade : 1 : générateur principal , 2: genérateur secondaire etc
 */
final class PrioriteCascade extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur);
    }
}
