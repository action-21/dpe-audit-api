<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Service\Assert;

final class EnsoleillementBaie
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
        Assert::positif_ou_zero($fe);
        Assert::positif_ou_zero($t);
        Assert::positif_ou_zero($c1);
        Assert::positif_ou_zero($sst);

        return new self(mois: $mois, fe: $fe, t: $t, c1: $c1, sst: $sst);
    }
}
