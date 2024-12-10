<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use Webmozart\Assert\Assert;

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
        Assert::greaterThanEq($besoin, 0);
        return new self(
            usage: $usage,
            scenario: $scenario,
            mois: $mois,
            besoin: $besoin,
        );
    }
}
