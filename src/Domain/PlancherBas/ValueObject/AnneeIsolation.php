<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\ValueObject\Entier;

/**
 * Année d'isolation
 */
final class AnneeIsolation extends Entier
{
    public static function from(int $valeur): static
    {
        return static::_from(valeur: $valeur, min: 1900, max: \date("Y"));
    }
}
