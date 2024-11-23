<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\EtatPerformance;
use App\Domain\Common\Service\Assert;

final class Performance
{
    public function __construct(
        public readonly float $u,
        public readonly float $b,
        public readonly float $dp,
        public readonly EtatPerformance $etat,
    ) {}

    public static function create(float $u, float $b, float $dp): self
    {
        Assert::positif($u);
        Assert::positif_ou_zero($b);
        Assert::positif_ou_zero($dp);

        return new self(u: $u, b: $b, dp: $dp, etat: EtatPerformance::from_ubaie(ubaie: $u),);
    }
}
