<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Nombre;

/**
 * Orientation de la baie en °
 */
final class OrientationBaie extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur, positive: false, min: 0, max: 360);
    }

    public function enum(): Orientation
    {
        return Orientation::from_azimut($this->valeur());
    }
}
