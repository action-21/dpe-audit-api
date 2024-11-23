<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Common\Service\Assert;

final class Rendement
{
    public function __construct(
        public readonly ScenarioUsage $scenario,
        public readonly float $fecs,
        public readonly float $iecs,
        public readonly float $rd,
        public readonly float $rs,
        public readonly float $rg,
        public readonly float $rgs,
    ) {}

    public static function create(
        ScenarioUsage $scenario,
        float $fecs,
        float $iecs,
        float $rd,
        float $rs,
        float $rg,
        float $rgs,
    ): self {
        Assert::positif_ou_zero($fecs);
        Assert::positif_ou_zero($iecs);
        Assert::positif_ou_zero($rd);
        Assert::positif_ou_zero($rs);
        Assert::positif_ou_zero($rg);
        Assert::positif_ou_zero($rgs);

        return new static(
            scenario: $scenario,
            fecs: $fecs,
            iecs: $iecs,
            rd: $rd,
            rs: $rs,
            rg: $rg,
            rgs: $rgs,
        );
    }
}
