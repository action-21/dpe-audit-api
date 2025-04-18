<?php

namespace App\Domain\Enveloppe\ValueObject\Baie;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Pourcentage;
use Webmozart\Assert\Assert;

final class Ensoleillement
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $fe,
        public readonly Pourcentage $sw,
        public readonly float $c1,
        public readonly float $sse,
    ) {}

    public static function create(Mois $mois, float $fe, Pourcentage $sw, float $c1, float $sse): self
    {
        Assert::greaterThanEq($fe, 0);
        Assert::greaterThanEq($c1, 0);
        Assert::greaterThanEq($sse, 0);

        return new self(mois: $mois, fe: $fe, sw: $sw, c1: $c1, sse: $sse);
    }
}
