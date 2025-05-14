<?php

namespace App\Api\Enveloppe\Model;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Enveloppe\ValueObject\Apports as Value;

final class Apport
{
    public function __construct(
        public ScenarioUsage $scenario,
        public float $f,
        public float $apport_interne,
        public float $apport_interne_fr,
        public float $apport_solaire,
        public float $apport_solaire_fr,
        public float $sse,
    ) {}

    /**
     * @return self[]
     */
    public static function from(Value $value): array
    {
        $values = [];

        foreach ($value->scenarios() as $scenario) {
            $values[] = new self(
                scenario: $scenario,
                f: $value->f(scenario: $scenario),
                apport_interne: $value->apports_internes(scenario: $scenario),
                apport_solaire: $value->apports_solaires(scenario: $scenario),
                apport_interne_fr: $value->apports_internes_fr(scenario: $scenario),
                apport_solaire_fr: $value->apports_solaires_fr(scenario: $scenario),
                sse: $value->sse(scenario: $scenario),
            );
        }
        return $values;
    }
}
