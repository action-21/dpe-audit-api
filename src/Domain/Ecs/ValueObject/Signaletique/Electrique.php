<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\LabelGenerateur;
use App\Domain\Ecs\ValueObject\Signaletique;

final class Electrique extends Signaletique
{
    public static function create(LabelGenerateur $label, ?float $pn,): static
    {
        return new self(label: $label, pn: $pn);
    }
}
