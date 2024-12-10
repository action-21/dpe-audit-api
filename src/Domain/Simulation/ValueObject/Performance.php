<?php

namespace App\Domain\Simulation\ValueObject;

use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly Usage $usage,
        public readonly float $consommation_ef,
        public readonly float $consommation_ep,
        public readonly float $emission,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        Usage $usage,
        float $consommation_ef,
        float $consommation_ep,
        float $emission,
    ): self {
        Assert::greaterThanEq($consommation_ef, 0);
        Assert::greaterThanEq($consommation_ep, 0);
        Assert::greaterThanEq($emission, 0);

        return new self(
            scenario: $scenario,
            usage: $usage,
            consommation_ef: $consommation_ef,
            consommation_ep: $consommation_ep,
            emission: $emission,
        );
    }
}
