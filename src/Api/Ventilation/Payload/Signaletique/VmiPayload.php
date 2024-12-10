<?php

namespace App\Api\Ventilation\Payload\Signaletique;

use App\Domain\Ventilation\Enum\TypeGenerateur;
use App\Domain\Ventilation\ValueObject\Signaletique;

final class VmiPayload
{
    public function __construct(
        public TypeGenerateur\Vmi $type,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_vmi();
    }
}
