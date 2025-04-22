<?php

namespace App\Domain\Ventilation\Data;

use App\Domain\Common\ValueObject\{Consommation, Consommations, Emission, Emissions};

final class GenerateurData
{
    public function __construct(
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
