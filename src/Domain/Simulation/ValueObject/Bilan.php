<?php

namespace App\Domain\Simulation\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Simulation\Enum\Etiquette;

final class Bilan
{
    public function __construct(
        public readonly float $consommation,
        public readonly float $emission,
        public readonly Etiquette $etiquette_energie,
        public readonly Etiquette $etiquette_climat,
    ) {}

    public static function create(
        float $consommation,
        float $emission,
        Etiquette $etiquette_energie,
        Etiquette $etiquette_climat,
    ): self {
        Assert::positif_ou_zero($emission);

        return new self(
            consommation: $consommation,
            emission: $emission,
            etiquette_energie: $etiquette_energie,
            etiquette_climat: $etiquette_climat,
        );
    }
}
