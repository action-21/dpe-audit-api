<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\ValueObject\Signaletique;

final class Combustion extends Signaletique
{
    public static function create(
        ?bool $presence_ventouse = null,
        ?float $pn = null,
        ?float $rpn = null,
        ?float $qp0 = null,
    ): static {
        return new self(
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            qp0: $qp0,
        );
    }
}
