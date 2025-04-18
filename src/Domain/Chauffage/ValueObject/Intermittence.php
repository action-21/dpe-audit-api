<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

final class Intermittence
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $i0,
        public readonly float $int,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $i0,
        float $int,
    ): self {
        Assert::greaterThanEq($i0, 0);
        Assert::greaterThanEq($int, 0);
        return new self($scenario, $i0, $int);
    }
}
