<?php

namespace App\Domain\Audit\ValueObject;

use Webmozart\Assert\Assert;

final class Occupation
{
    public function __construct(
        public readonly float $nadeq,
        public readonly float $nmax,
    ) {}

    public static function create(float $nadeq, float $nmax): self
    {
        Assert::greaterThanEq($nadeq, 0);
        Assert::greaterThanEq($nmax, 0);
        return new self(nadeq: $nadeq, nmax: $nmax,);
    }
}
