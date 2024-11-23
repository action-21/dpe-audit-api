<?php

namespace App\Domain\Chauffage\ValueObject\Signaletique;

use App\Domain\Chauffage\Enum\TypeChaudiere;
use App\Domain\Chauffage\ValueObject\Signaletique;

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
