<?php

namespace App\Domain\PontThermique\ValueObject;

use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly float $kpt,
        public readonly float $pt,
    ) {}

    public static function create(float $kpt, float $pt): self
    {
        Assert::greaterThanEq($kpt, 0);
        Assert::greaterThanEq($pt, 0);
        return new self(kpt: $kpt, pt: $pt);
    }
}
