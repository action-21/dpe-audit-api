<?php

namespace App\Api\Ventilation\Payload\Signaletique;

use App\Domain\Ventilation\Enum\TypeGenerateur;
use App\Domain\Ventilation\ValueObject\Signaletique;

final class PuitClimatiquePayload
{
    public function __construct(
        public TypeGenerateur\PuitClimatique $type,
        public bool $presence_echangeur_thermique,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_puit_climatique(presence_echangeur_thermique: $this->presence_echangeur_thermique);
    }
}
