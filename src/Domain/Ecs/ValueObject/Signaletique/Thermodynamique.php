<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\ValueObject\Signaletique;

final class Thermodynamique extends Signaletique
{
    public static function create(?float $pn, ?float $cop): static
    {
        return new self(pn: $pn, cop: $cop);
    }
}
