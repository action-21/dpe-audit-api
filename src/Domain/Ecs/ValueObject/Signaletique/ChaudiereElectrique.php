<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\TypeChaudiere;
use App\Domain\Ecs\ValueObject\Signaletique;

final class ChaudiereElectrique extends Signaletique
{
    public static function create(
        TypeChaudiere $type_chaudiere,
        ?float $pn,
    ): static {
        return new self(
            type_chaudiere: $type_chaudiere,
            pn: $pn,
        );
    }
}
