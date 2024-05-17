<?php

namespace App\Domain\Photovoltaique\ValueObject;

use App\Domain\Common\ValueObject\Nombre;
use App\Domain\Photovoltaique\Enum\InclinaisonCapteur as Enum;

/**
 * Inclinaison du capteur en °
 */
final class InclinaisonCapteur extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur, positive: false, min: 0, max: 90);
    }

    public function enum(): Enum
    {
        return Enum::from($this->valeur());
    }
}
