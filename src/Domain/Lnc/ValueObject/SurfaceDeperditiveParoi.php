<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\ValueObject\ValeursForfaitaires;
use App\Domain\Lnc\Enum\EtatIsolation;
use Webmozart\Assert\Assert;

final class SurfaceDeperditiveParoi
{
    public function __construct(
        public readonly float $aue,
        public readonly float $aiu,
        public readonly EtatIsolation $isolation,
        public readonly ValeursForfaitaires $valeurs_forfaitaires,
    ) {}

    public static function create(
        float $aue,
        float $aiu,
        EtatIsolation $isolation,
        ValeursForfaitaires $valeurs_forfaitaires,
    ): self {
        Assert::greaterThanEq($aue, 0);
        Assert::greaterThanEq($aiu, 0);
        return new self(aue: $aue, aiu: $aiu, isolation: $isolation, valeurs_forfaitaires: $valeurs_forfaitaires);
    }
}
