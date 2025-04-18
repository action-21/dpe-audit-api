<?php

namespace App\Domain\Enveloppe\ValueObject\Lnc;

use App\Domain\Common\Enum\Mois;
use Webmozart\Assert\Assert;

final class EnsoleillementBaie
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $fe,
        public readonly float $t,
        public readonly float $c1,
        public readonly float $sst,
    ) {}

    public static function create(
        Mois $mois,
        float $fe,
        float $t,
        float $c1,
        float $sst,
    ): self {
        Assert::nullOrGreaterThanEq($fe, 0);
        Assert::nullOrGreaterThanEq($t, 0);
        Assert::nullOrGreaterThanEq($c1, 0);
        Assert::nullOrGreaterThanEq($sst, 0);

        return new self(
            mois: $mois,
            fe: $fe,
            t: $t,
            c1: $c1,
            sst: $sst,
        );
    }
}
