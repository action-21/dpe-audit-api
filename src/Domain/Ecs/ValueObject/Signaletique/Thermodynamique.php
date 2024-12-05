<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\ValueObject\Signaletique;
use Webmozart\Assert\Assert;

final class Thermodynamique extends Signaletique
{
    public static function create(?float $pn, ?float $cop): static
    {
        Assert::greaterThan($pn, 0);
        Assert::greaterThan($cop, 0);
        return new self(pn: $pn, cop: $cop);
    }
}
