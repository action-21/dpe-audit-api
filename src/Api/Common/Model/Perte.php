<?php

namespace App\Api\Common\Model;

use App\Domain\Common\Enum\{ScenarioUsage, TypePerte, Usage};
use App\Domain\Common\ValueObject\Pertes as Value;

final class Perte
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ScenarioUsage $scenario,
        public readonly TypePerte $type,
        public readonly float $perte,
    ) {}

    /**
     * @return self[]
     */
    public static function from(Value $value): array
    {
        $values = [];

        foreach ($value->usages() as $usage) {
            foreach ($value->scenarios() as $scenario) {
                foreach ($value->types() as $type) {
                    $values[] = new self(
                        usage: $usage,
                        scenario: $scenario,
                        type: $type,
                        perte: $value->get(scenario: $scenario, usage: $usage, type: $type),
                    );
                }
            }
        }
        return $values;
    }
}
