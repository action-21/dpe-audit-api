<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use Webmozart\Assert\Assert;

final class Perte
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly Mois $mois,
        public readonly float $pertes,
        public readonly float $pertes_recuperables,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        Mois $mois,
        float $pertes,
        float $pertes_recuperables,
    ): self {
        Assert::greaterThanEq($pertes, 0);
        Assert::greaterThanEq($pertes_recuperables, 0);

        return new static(
            scenario: $scenario,
            mois: $mois,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
        );
    }
}
