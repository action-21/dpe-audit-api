<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use Webmozart\Assert\Assert;

final class Permeabilite
{
    public function __construct(
        public readonly bool $presence_joints_menuiserie,
        public readonly EtatIsolation $isolation_murs_plafonds,
        public readonly float $hvent,
        public readonly float $hperm,
        public readonly float $q4pa_conv,
    ) {}

    public static function create(
        bool $presence_joints_menuiserie,
        EtatIsolation $isolation_murs_plafonds,
        float $hvent,
        float $hperm,
        float $q4pa_conv,
    ): self {
        Assert::greaterThanEq($hvent, 0);
        Assert::greaterThanEq($hperm, 0);
        Assert::greaterThanEq($q4pa_conv, 0);

        return new self(
            presence_joints_menuiserie: $presence_joints_menuiserie,
            isolation_murs_plafonds: $isolation_murs_plafonds,
            hvent: $hvent,
            hperm: $hperm,
            q4pa_conv: $q4pa_conv,
        );
    }
}
