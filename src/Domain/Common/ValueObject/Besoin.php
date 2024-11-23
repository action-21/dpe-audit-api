<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\Service\Assert;

final class Besoin
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ScenarioUsage $scenario,
        public readonly Mois $mois,
        public readonly float $besoin,
    ) {}

    public static function create(
        Usage $usage,
        ScenarioUsage $scenario,
        Mois $mois,
        float $besoin
    ): self {
        Assert::positif_ou_zero($besoin);
        return new self(
            usage: $usage,
            scenario: $scenario,
            mois: $mois,
            besoin: $besoin,
        );
    }
}
