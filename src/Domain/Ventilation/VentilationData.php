<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\ValueObject\{Consommations, Emissions};
use Webmozart\Assert\Assert;

final class VentilationData
{
    public function __construct(
        public readonly ?float $qvarep_conv = null,
        public readonly ?float $qvasouf_conv = null,
        public readonly ?float $smea_conv = null,
        public readonly ?Consommations $consommations = null,
        public readonly ?Emissions $emissions = null,
    ) {}

    public static function create(
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        Assert::nullOrGreaterThanEq($qvarep_conv, 0);
        Assert::nullOrGreaterThanEq($qvasouf_conv, 0);
        Assert::nullOrGreaterThanEq($smea_conv, 0);

        return new self(
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(
            qvarep_conv: $qvarep_conv ?? $this->qvarep_conv,
            qvasouf_conv: $qvasouf_conv ?? $this->qvasouf_conv,
            smea_conv: $smea_conv ?? $this->smea_conv,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
