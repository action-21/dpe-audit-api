<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Common\ValueObject\{Consommations, Emissions, Pertes};
use App\Domain\Ecs\ValueObject\Rendements;
use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?Rendements $rendements,
        public readonly ?Pertes $pertes,
        public readonly ?Pertes $pertes_recuperables,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?Rendements $rendements = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);

        return new self(
            rdim: $rdim,
            rendements: $rendements,
            pertes: $pertes,
            pertes_recuperables: $pertes_recuperables,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $rdim = null,
        ?Rendements $rendements = null,
        ?Pertes $pertes = null,
        ?Pertes $pertes_recuperables = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            rendements: $rendements ?? $this->rendements,
            pertes: $pertes ? ($this->pertes?->merge($pertes) ?? $pertes) : $this->pertes,
            pertes_recuperables: $pertes_recuperables ? ($this->pertes_recuperables?->merge($pertes_recuperables) ?? $pertes_recuperables) : $this->pertes_recuperables,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
