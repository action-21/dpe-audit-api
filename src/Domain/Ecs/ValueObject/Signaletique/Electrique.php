<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\LabelGenerateur;
use App\Domain\Ecs\ValueObject\Signaletique;
use Webmozart\Assert\Assert;

final class Electrique extends Signaletique
{
    public static function create(LabelGenerateur $label, ?float $pn,): static
    {
        Assert::greaterThan($pn, 0);
        return new self(label: $label, pn: $pn);
    }
}
