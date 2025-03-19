<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\ValueObject\ValeursForfaitaires;
use App\Domain\Lnc\Enum\EtatIsolation;
use Webmozart\Assert\Assert;

final class SurfaceDeperditive
{
    public function __construct(
        public readonly float $aue,
        public readonly float $aiu,
        public readonly EtatIsolation $isolation_aue,
        public readonly EtatIsolation $isolation_aiu,
        public readonly ValeursForfaitaires $valeurs_forfaitaires,
    ) {}

    public static function create(
        float $aue,
        float $aiu,
        EtatIsolation $isolation_aue,
        EtatIsolation $isolation_aiu,
        ValeursForfaitaires $valeurs_forfaitaires,
    ): self {
        Assert::greaterThanEq($aue, 0);
        Assert::greaterThanEq($aiu, 0);
        return new self(
            aue: $aue,
            aiu: $aiu,
            isolation_aue: $isolation_aue,
            isolation_aiu: $isolation_aiu,
            valeurs_forfaitaires: $valeurs_forfaitaires,
        );
    }
}
