<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;

/**
 * @property Consommation[] $values
 */
final class Consommations
{
    public function __construct(public readonly array $values) {}

    public static function from(Consommation ...$values): self
    {
        return new self($values);
    }

    public function get(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, bool $energie_primaire = false): float
    {
        $values = array_filter($this->values, fn(Consommation $item) => $item->scenario === $scenario);
        return array_reduce($values, fn(float $carry, Consommation $item) => $carry + $energie_primaire ? $item->consommation_ep : $item->consommation_ef, 0);
    }

    /**
     * @return Consommation[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
