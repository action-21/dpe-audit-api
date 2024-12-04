<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Mur\Enum\EtatPerformance;
use Webmozart\Assert\Assert;

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
        Assert::greaterThan($u0, 0);
        Assert::greaterThan($u, 0);
        Assert::greaterThanEq($b, 0);
        Assert::greaterThanEq($dp, 0);
        return new self(u0: $u0, u: $u, b: $b, dp: $dp, etat: EtatPerformance::from_umur(umur: $u));
    }
}
