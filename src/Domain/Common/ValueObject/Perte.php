<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, TypePerte, Usage};
use Webmozart\Assert\Assert;

final class Perte
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ScenarioUsage $scenario,
        public readonly Mois $mois,
        public readonly TypePerte $type,
        public readonly float $perte,
    ) {}

    public static function create(
        Usage $usage,
        ScenarioUsage $scenario,
        Mois $mois,
        TypePerte $type,
        float $perte,
    ): self {
        Assert::greaterThanEq($perte, 0);

        return new static(
            scenario: $scenario,
            usage: $usage,
            mois: $mois,
            type: $type,
            perte: $perte,
        );
    }
}
