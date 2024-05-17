<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\ValueObject\Nombre;
use App\Domain\Baie\Enum\InclinaisonVitrage as InclinaisonVitrageEnum;

/**
 * Inclinaison du vitrage exprimée en °
 */
final class InclinaisonVitrage extends Nombre
{
    public static function from(float $valeur): static
    {
        return static::_from($valeur, positive: false, min: 0, max: 90);
    }

    public function enum(): InclinaisonVitrageEnum
    {
        return InclinaisonVitrageEnum::from_angle($this->valeur());
    }
}
