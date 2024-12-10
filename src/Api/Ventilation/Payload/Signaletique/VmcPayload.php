<?php

namespace App\Api\Ventilation\Payload\Signaletique;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};
use App\Domain\Ventilation\ValueObject\Signaletique;

final class VmcPayload
{
    public function __construct(
        public TypeGenerateur\Vmc $type,
        public TypeVmc $type_vmc,
        public bool $presence_echangeur_thermique,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_vmc(
            type: $this->type,
            type_vmc: $this->type_vmc,
            presence_echangeur_thermique: $this->presence_echangeur_thermique,
        );
    }
}
