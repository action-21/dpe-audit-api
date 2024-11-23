<?php

namespace App\Domain\PlancherHaut\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\PlancherHaut\Enum\EtatPerformance;

final class Performance
{
    public function __construct(
        public readonly float $u0,
        public readonly float $u,
        public readonly float $b,
        public readonly float $dp,
        public readonly EtatPerformance $etat,
    ) {}

    public static function create(float $u0, float $u, float $b, float $dp): self
    {
        Assert::positif($u0);
        Assert::positif($u);
        Assert::positif_ou_zero($b);
        Assert::positif_ou_zero($dp);
        return new self(u0: $u0, u: $u, b: $b, dp: $dp, etat: EtatPerformance::from_uph(uph: $u));
    }
}
