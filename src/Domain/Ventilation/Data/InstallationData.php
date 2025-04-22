<?php

namespace App\Domain\Ventilation\Data;

use App\Domain\Common\ValueObject\{Consommations, Emissions};
use Webmozart\Assert\Assert;

final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $qvarep_conv,
        public readonly ?float $qvasouf_conv,
        public readonly ?float $smea_conv,
        public readonly ?Consommations $consommations,
        public readonly ?Emissions $emissions,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($qvarep_conv, 0);
        Assert::nullOrGreaterThanEq($qvasouf_conv, 0);
        Assert::nullOrGreaterThanEq($smea_conv, 0);

        return new self(
            rdim: $rdim,
            qvarep_conv: $qvarep_conv,
            qvasouf_conv: $qvasouf_conv,
            smea_conv: $smea_conv,
            consommations: $consommations,
            emissions: $emissions,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $qvarep_conv = null,
        ?float $qvasouf_conv = null,
        ?float $smea_conv = null,
        ?Consommations $consommations = null,
        ?Emissions $emissions = null,
    ): self {
        return new self(
            rdim: $rdim ?? $this->rdim,
            qvarep_conv: $qvarep_conv ?? $this->qvarep_conv,
            qvasouf_conv: $qvasouf_conv ?? $this->qvasouf_conv,
            smea_conv: $smea_conv ?? $this->smea_conv,
            consommations: $consommations ? ($this->consommations?->merge($consommations) ?? $consommations) : $this->consommations,
            emissions: $emissions ? ($this->emissions?->merge($emissions) ?? $emissions) : $this->emissions,
        );
    }
}
