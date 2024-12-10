<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

final class Rendement
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $fch,
        public readonly float $i0,
        public readonly float $int,
        public readonly float $ich,
        public readonly float $rg,
        public readonly float $rd,
        public readonly float $re,
        public readonly float $rr,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $fch,
        float $i0,
        float $int,
        float $ich,
        float $rg,
        float $rd,
        float $re,
        float $rr,
    ): self {
        Assert::greaterThanEq($fch, 0);
        Assert::greaterThanEq($i0, 0);
        Assert::greaterThanEq($int, 0);
        Assert::greaterThanEq($ich, 0);
        Assert::greaterThanEq($rg, 0);
        Assert::greaterThanEq($rd, 0);
        Assert::greaterThanEq($re, 0);
        Assert::greaterThanEq($rr, 0);

        return new static(
            scenario: $scenario,
            fch: $fch,
            i0: $i0,
            int: $int,
            ich: $ich,
            rg: $rg,
            rd: $rd,
            re: $re,
            rr: $rr,
        );
    }
}
