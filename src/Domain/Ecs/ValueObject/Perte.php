<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Common\Service\Assert;
use App\Domain\Ecs\Enum\TypePerte;

final class Perte
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly Mois $mois,
        public readonly TypePerte $type,
        public readonly float $pertes,
        public readonly float $pertes_recuperables,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        Mois $mois,
        TypePerte $type,
        float $pertes,
        float $pertes_recuperables,
    ): self {
        Assert::positif_ou_zero($pertes);
        Assert::positif_ou_zero($pertes_recuperables);

        return new static(
            scenario: $scenario,
            mois: $mois,
            type: $type,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
        );
    }
}
