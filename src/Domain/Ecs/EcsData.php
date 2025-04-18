<?php

namespace App\Domain\Ecs;

use App\Domain\Common\ValueObject\{Besoins, Consommations, Emissions, Pertes};
use Webmozart\Assert\Assert;

final class EcsData
{
    public function __construct(
        public readonly ?float $nmax,
        public readonly ?float $nadeq,
        public readonly ?Besoins $besoins,
        public readonly ?Pertes $pertes,
        public readonly ?Pertes $pertes_recuperables,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?Besoins $besoins = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        Assert::nullOrGreaterThan($nmax, 0);
        Assert::nullOrGreaterThan($nadeq, 0);

        return new self(
            nmax: $nmax,
            nadeq: $nadeq,
            besoins: $besoins,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?Besoins $besoins = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            nmax: $nmax ?? $this->nmax,
            nadeq: $nadeq ?? $this->nadeq,
            besoins: $besoins ?? $this->besoins,
            pertes: $pertes ? ($this->pertes?->merge($pertes) ?? $pertes) : $this->pertes,
            pertes_recuperables: $pertes_recuperables ? ($this->pertes_recuperables?->merge($pertes_recuperables) ?? $pertes_recuperables) : $this->pertes_recuperables,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
