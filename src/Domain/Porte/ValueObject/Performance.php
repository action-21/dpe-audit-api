<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Common\ValueObject\ValeursForfaitaires;
use App\Domain\Porte\Enum\EtatPerformance;
use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly float $sdep,
        public readonly float $u,
        public readonly float $b,
        public readonly float $dp,
        public readonly EtatPerformance $etat_performance,
        public readonly ValeursForfaitaires $valeurs_forfaitaires,
    ) {}

    public static function create(
        float $sdep,
        float $u,
        float $b,
        float $dp,
        EtatPerformance $etat_performance,
        ValeursForfaitaires $valeurs_forfaitaires,
    ): self {
        Assert::greaterThanEq($sdep, 0);
        Assert::greaterThanEq($u, 0);
        Assert::greaterThanEq($b, 0);
        Assert::greaterThanEq($dp, 0);

        return new self(
            sdep: $sdep,
            u: $u,
            b: $b,
            dp: $dp,
            etat_performance: $etat_performance,
            valeurs_forfaitaires: $valeurs_forfaitaires,
        );
    }
}
