<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

final class Rendement
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $iecs,
        public readonly float $rd,
        public readonly float $rs,
        public readonly float $rg,
        public readonly float $rgs,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $iecs,
        float $rd,
        float $rs,
        float $rg,
        float $rgs,
    ): self {
        Assert::greaterThanEq($iecs, 0);
        Assert::greaterThanEq($rd, 0);
        Assert::greaterThanEq($rs, 0);
        Assert::greaterThanEq($rg, 0);
        Assert::greaterThanEq($rgs, 0);

        return new self(
            scenario: $scenario,
            iecs: $iecs,
            rd: $rd,
            rs: $rs,
            rg: $rg,
            rgs: $rgs,
        );
    }
}
