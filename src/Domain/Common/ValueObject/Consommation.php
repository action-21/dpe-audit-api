<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Energie, ScenarioUsage, Usage};
use App\Domain\Common\Service\Assert;

/**
 * Les consommations sont exprimées sur l'année
 */
final class Consommation
{
    public function __construct(
        public readonly Usage $usage,
        public readonly Energie $energie,
        public readonly ScenarioUsage $scenario,
        public readonly float $consommation_ef,
        public readonly float $consommation_ep,
    ) {}

    public static function create(
        Usage $usage,
        Energie $energie,
        ScenarioUsage $scenario,
        float $consommation,
    ): self {
        Assert::positif_ou_zero($consommation);
        return new self(
            usage: $usage,
            energie: $energie,
            scenario: $scenario,
            consommation_ef: $consommation,
            consommation_ep: $consommation * $energie->facteur_energie_primaire(),
        );
    }
}
