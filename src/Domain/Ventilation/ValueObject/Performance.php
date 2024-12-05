<?php

namespace App\Domain\Ventilation\ValueObject;

use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly float $qvarep_conv,
        public readonly float $qvasouf_conv,
        public readonly float $smea_conv,
        public readonly float $ratio_utilisation,
        public readonly float $pvent_moy,
        public readonly float $pvent,
    ) {}

    public static function create(
        float $qvarep_conv,
        float $qvasouf_conv,
        float $smea_conv,
        float $ratio_utilisation,
        float $pvent_moy,
        float $pvent,
    ): self {
        Assert::greaterThanEq($qvarep_conv, 0);
        Assert::greaterThanEq($qvasouf_conv, 0);
        Assert::greaterThanEq($smea_conv, 0);
        Assert::greaterThanEq($ratio_utilisation, 0);
        Assert::greaterThanEq($pvent_moy, 0);
        Assert::greaterThanEq($pvent, 0);

        return new self(
            qvarep_conv: $qvarep_conv,
            qvasouf_conv: $qvasouf_conv,
            smea_conv: $smea_conv,
            ratio_utilisation: $ratio_utilisation,
            pvent_moy: $pvent_moy,
            pvent: $pvent,
        );
    }
}
