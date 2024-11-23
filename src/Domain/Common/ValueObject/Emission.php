<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\Service\Assert;

final class Emission
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ScenarioUsage $scenario,
        public readonly Mois $mois,
        public readonly float $emission,
    ) {}

    public static function create(
        Usage $usage,
        ScenarioUsage $scenario,
        Mois $mois,
        float $emission
    ): self {
        Assert::positif_ou_zero($emission);
        return new self(
            usage: $usage,
            scenario: $scenario,
            mois: $mois,
            emission: $emission,
        );
    }
}
