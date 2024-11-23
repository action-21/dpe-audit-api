<?php

namespace App\Domain\Chauffage\ValueObject\Signaletique;

use App\Domain\Chauffage\ValueObject\Signaletique;

final class Pac extends Signaletique
{
    public static function create(?float $pn, ?float $scop): static
    {
        return new self(pn: $pn, scop: $scop);
    }
}
