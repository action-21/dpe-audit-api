<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

final class Emission
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $emission,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $emission,
    ): self {
        Assert::greaterThanEq($emission, 0);
        return new self($scenario, $emission);
    }
}
