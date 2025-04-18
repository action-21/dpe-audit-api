<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

final class Rendement
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $rendement,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $rendement,
    ): self {
        Assert::greaterThanEq($rendement, 0);

        return new static(
            scenario: $scenario,
            rendement: $rendement,
        );
    }
}
