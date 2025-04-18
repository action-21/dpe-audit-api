<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use Webmozart\Assert\Assert;

final class Apport
{
    public function __construct(
        public readonly Mois $mois,
        public readonly ScenarioUsage $scenario,
        public readonly float $f,
        public readonly float $apport_interne,
        public readonly float $apport_interne_fr,
        public readonly float $apport_solaire,
        public readonly float $apport_solaire_fr,
        public readonly float $sse,
    ) {}

    public static function create(
        Mois $mois,
        ScenarioUsage $scenario,
        float $f,
        float $apport_interne,
        float $apport_solaire,
        float $apport_interne_fr,
        float $apport_solaire_fr,
        float $sse,
    ): self {
        Assert::greaterThanEq($f, 0);
        Assert::greaterThanEq($apport_interne, 0);
        Assert::greaterThanEq($apport_solaire, 0);
        Assert::greaterThanEq($apport_interne_fr, 0);
        Assert::greaterThanEq($apport_solaire_fr, 0);
        Assert::greaterThanEq($sse, 0);

        return new self(
            mois: $mois,
            scenario: $scenario,
            f: $f,
            apport_interne: $apport_interne,
            apport_solaire: $apport_solaire,
            apport_interne_fr: $apport_interne_fr,
            apport_solaire_fr: $apport_solaire_fr,
            sse: $sse,
        );
    }

    public function apport(): float
    {
        return $this->apport_interne + $this->apport_solaire;
    }

    public function apport_fr(): float
    {
        return $this->apport_interne_fr + $this->apport_solaire_fr;
    }
}
