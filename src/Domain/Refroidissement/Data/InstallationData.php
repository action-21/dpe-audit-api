<?php

namespace App\Domain\Refroidissement\Data;

use App\Domain\Common\ValueObject\{Consommations, Emissions};
use Webmozart\Assert\Assert;

final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        Assert::nullOrGreaterThan($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        return new self(rdim: $rdim, consommations: $consommations, emissions: $emissions,);
    }

    public function with(
        ?float $rdim = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
