<?php

namespace App\Domain\Eclairage;

use App\Domain\Common\ValueObject\{Consommations, Emissions};

final class EclairageData
{
    public function __construct(
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(consommations: $consommations, emissions: $emissions);
    }

    public function with(
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            consommations: $consommations ?? $this->consommations,
            emissions: $emissions ?? $this->emissions,
        );
    }
}
