<?php

namespace App\Api\Ventilation\Payload\Signaletique;

use App\Domain\Ventilation\Enum\TypeGenerateur;
use App\Domain\Ventilation\ValueObject\Signaletique;

final class VmrPayload
{
    public function __construct(
        public TypeGenerateur\Vmr $type,
    ) {}

    public function to(): Signaletique
    {
        return Signaletique::create_vmr();
    }
}
