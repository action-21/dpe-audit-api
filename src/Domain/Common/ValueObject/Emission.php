<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use Webmozart\Assert\Assert;

final class Emission
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ScenarioUsage $scenario,
        public readonly float $eges,
    ) {}

    public static function create(
        Usage $usage,
        ScenarioUsage $scenario,
        float $eges
    ): self {
        Assert::greaterThanEq($eges, 0);
        return new self(
            usage: $usage,
            scenario: $scenario,
            eges: $eges,
        );
    }
}
