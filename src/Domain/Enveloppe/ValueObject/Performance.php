<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\EtatPerformance;
use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly float $ubat,
        public readonly float $dp,
        public readonly float $pt,
        public readonly float $dr,
        public readonly float $gv,
        public readonly EtatPerformance $etat,
    ) {}

    public static function create(float $sdep, float $dp, float $pt, float $dr, float $gv): self
    {
        Assert::greaterThanEq($sdep, 0);
        Assert::greaterThanEq($dp, 0);
        Assert::greaterThanEq($pt, 0);
        Assert::greaterThanEq($dr, 0);
        Assert::greaterThanEq($gv, 0);

        return new self(
            ubat: ($ubat = ($dp + $dr) / $sdep),
            dp: $dp,
            pt: $pt,
            dr: $dr,
            gv: $gv,
            etat: EtatPerformance::from_ubat($ubat),
        );
    }
}
