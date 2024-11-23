<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Common\Service\Assert;

final class Rendement
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $fch,
        public readonly float $i0,
        public readonly float $int,
        public readonly float $ich,
        public readonly float $rg,
        public readonly float $rd,
        public readonly float $re,
        public readonly float $rr,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $fch,
        float $i0,
        float $int,
        float $ich,
        float $rg,
        float $rd,
        float $re,
        float $rr,
    ): self {
        Assert::positif_ou_zero($fch);
        Assert::positif_ou_zero($i0);
        Assert::positif_ou_zero($int);
        Assert::positif_ou_zero($ich);
        Assert::positif_ou_zero($rg);
        Assert::positif_ou_zero($rd);
        Assert::positif_ou_zero($re);
        Assert::positif_ou_zero($rr);

        return new static(
            scenario: $scenario,
            fch: $fch,
            i0: $i0,
            int: $int,
            ich: $ich,
            rg: $rg,
            rd: $rd,
            re: $re,
            rr: $rr,
        );
    }
}
