<?php

namespace App\Domain\Chauffage\ValueObject\Signaletique;

use App\Domain\Chauffage\ValueObject\Signaletique;

final class Combustion extends Signaletique
{
    public static function create(
        ?float $pn,
        ?float $rpn,
        ?float $rpint,
        ?float $qp0,
    ): static {
        return new self(
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
        );
    }
}
