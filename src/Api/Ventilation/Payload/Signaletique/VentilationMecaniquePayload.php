<?php

namespace App\Api\Ventilation\Payload\Signaletique;

use App\Domain\Ventilation\Enum\TypeGenerateur;
use App\Domain\Ventilation\ValueObject\Signaletique;

final class VentilationMecaniquePayload
{
    public function __construct(
        public TypeGenerateur\VentilationMecanique $type,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_ventilation_mecanique();
    }
}
