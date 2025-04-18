<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\ConfigurationSysteme;
use App\Domain\Chauffage\ValueObject\{Intermittences, Rendements};
use App\Domain\Common\ValueObject\{Consommations, Emissions};

final class SystemeData
{
    public function __construct(
        public readonly ?ConfigurationSysteme $configuration,
        public readonly ?float $rdim,
        public readonly ?float $rd,
        public readonly ?float $re,
        public readonly ?float $rr,
        public readonly ?Rendements $rg,
        public readonly ?Rendements $ich,
        public readonly ?Intermittences $intermittences,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?ConfigurationSysteme $configuration = null,
        ?float $rdim = null,
        ?float $rd = null,
        ?float $re = null,
        ?float $rr = null,
        ?Rendements $rg = null,
        ?Rendements $ich = null,
        ?Intermittences $intermittences = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(
            configuration: $configuration,
            rdim: $rdim,
            rd: $rd,
            re: $re,
            rr: $rr,
            rg: $rg,
            ich: $ich,
            intermittences: $intermittences,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?ConfigurationSysteme $configuration = null,
        ?float $rdim = null,
        ?float $rd = null,
        ?float $re = null,
        ?float $rr = null,
        ?Rendements $rg = null,
        ?Rendements $ich = null,
        ?Intermittences $intermittences = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return self::create(
            configuration: $configuration ?? $this->configuration,
            rdim: $rdim ?? $this->rdim,
            rd: $rd ?? $this->rd,
            re: $re ?? $this->re,
            rr: $rr ?? $this->rr,
            rg: $rg ?? $this->rg,
            ich: $ich ?? $this->ich,
            intermittences: $intermittences ?? $this->intermittences,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
