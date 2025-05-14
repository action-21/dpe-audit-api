<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Energie, ScenarioUsage, Usage};
use Webmozart\Assert\Assert;

final class Consommation
{
    public function __construct(
        public readonly Usage $usage,
        public readonly Energie $energie,
        public readonly ScenarioUsage $scenario,
        public readonly float $cef,
        public readonly float $cep,
    ) {}

    public static function create(
        Usage $usage,
        Energie $energie,
        ScenarioUsage $scenario,
        float $cef,
    ): self {
        Assert::greaterThanEq($cef, 0);
        return new self(
            usage: $usage,
            energie: $energie,
            scenario: $scenario,
            cef: $cef,
            cep: $cef * $energie->facteur_energie_primaire(),
        );
    }
}
