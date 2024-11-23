<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Common\Service\Assert;

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
        Assert::positif_ou_zero($pertes);
        Assert::positif_ou_zero($pertes_recuperables);

        return new static(
            scenario: $scenario,
            mois: $mois,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
        );
    }
}
