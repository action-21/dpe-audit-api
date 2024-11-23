<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Enveloppe\Enum\EtatPerformance;

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
        Assert::positif_ou_zero($sdep);
        Assert::positif_ou_zero($dp);
        Assert::positif_ou_zero($pt);
        Assert::positif_ou_zero($dr);
        Assert::positif_ou_zero($gv);

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
