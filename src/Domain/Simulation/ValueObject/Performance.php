<?php

namespace App\Domain\Simulation\ValueObject;

use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\Service\Assert;

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
        Assert::positif_ou_zero($consommation_ef);
        Assert::positif_ou_zero($consommation_ep);
        Assert::positif_ou_zero($emission);

        return new self(
            scenario: $scenario,
            usage: $usage,
            consommation_ef: $consommation_ef,
            consommation_ep: $consommation_ep,
            emission: $emission,
        );
    }
}
