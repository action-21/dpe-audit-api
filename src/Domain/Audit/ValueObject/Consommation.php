<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

final class Consommation
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $consommation_ef,
        public readonly float $consommation_ep,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $consommation_ef,
        float $consommation_ep,
    ): self {
        Assert::greaterThanEq($consommation_ef, 0);
        Assert::greaterThanEq($consommation_ep, 0);
        return new self(
            scenario: $scenario,
            consommation_ef: $consommation_ef,
            consommation_ep: $consommation_ep,
        );
    }
}
