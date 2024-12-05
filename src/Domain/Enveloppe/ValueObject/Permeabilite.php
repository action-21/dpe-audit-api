<?php

namespace App\Domain\Enveloppe\ValueObject;

use Webmozart\Assert\Assert;

final class Permeabilite
{
    public function __construct(
        public readonly float $hvent,
        public readonly float $hperm,
        public readonly float $q4pa_conv,
    ) {}

    public static function create(float $hvent, float $hperm, float $q4pa_conv): self
    {
        Assert::greaterThanEq($hvent, 0);
        Assert::greaterThanEq($hperm, 0);
        Assert::greaterThanEq($q4pa_conv, 0);

        return new self(
            hvent: $hvent,
            hperm: $hperm,
            q4pa_conv: $q4pa_conv,
        );
    }
}
