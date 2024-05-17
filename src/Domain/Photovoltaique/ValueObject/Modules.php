<?php

namespace App\Domain\Photovoltaique\ValueObject;

use App\Domain\Common\ValueObject\Entier;

/**
 * Nombre de modules
 */
final class Modules extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from($valeur);
    }
}
