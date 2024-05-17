<?php

namespace App\Domain\Batiment\ValueObject;

use App\Domain\Batiment\Enum\ClasseAltitude;
use App\Domain\Common\ValueObject\Nombre;

/**
 * Altitude du bâtiment en m
 */
final class Altitude extends Nombre
{
    public static function from(float|int $valeur): static
    {
        return static::_from(valeur: $valeur, positive: false);
    }

    public function classe_altitude(): ClasseAltitude
    {
        return ClasseAltitude::from_altitude($this->valeur());
    }
}
