<?php

namespace App\Domain\Chauffage;

use App\Domain\Common\ValueObject\{Besoins, Consommations, Emissions, Pertes};

final class ChauffageData
{
    public function __construct(
        public readonly ?Besoins $besoins,
        public readonly ?Pertes $pertes,
        public readonly ?Pertes $pertes_recuperables,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?Besoins $besoins = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(
            besoins: $besoins,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?Besoins $besoins = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            besoins: $besoins ?? $this->besoins,
            pertes: $pertes ? ($this->pertes?->merge($pertes) ?? $pertes) : $this->pertes,
            pertes_recuperables: $pertes_recuperables ? ($this->pertes_recuperables?->merge($pertes_recuperables) ?? $pertes_recuperables) : $this->pertes_recuperables,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
