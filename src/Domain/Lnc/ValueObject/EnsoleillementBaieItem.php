<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Enum\Mois;
use Webmozart\Assert\Assert;

final class EnsoleillementBaieItem
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $fe,
        public readonly float $t,
        public readonly float $c1,
        public readonly float $sst,
    ) {}

    public static function create(Mois $mois, float $fe, float $t, float $c1, float $sst,): self
    {
        Assert::greaterThanEq($fe, 0);
        Assert::greaterThanEq($t, 0);
        Assert::greaterThanEq($c1, 0);
        Assert::greaterThanEq($sst, 0);

        return new self(mois: $mois, fe: $fe, t: $t, c1: $c1, sst: $sst);
    }
}
