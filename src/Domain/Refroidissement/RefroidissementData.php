<?php

namespace App\Domain\Refroidissement;

use App\Domain\Common\ValueObject\{Besoins, Consommations, Emissions};

final class RefroidissementData
{
    public function __construct(
        public readonly ?Besoins $besoins = null,
        public readonly ?Consommations $consommations = null,
        public readonly ?Emissions $emissions = null,
    ) {}

    public static function create(
        ?Besoins $besoins = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(
            besoins: $besoins,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?Besoins $besoins = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            besoins: $besoins ?? $this->besoins,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
