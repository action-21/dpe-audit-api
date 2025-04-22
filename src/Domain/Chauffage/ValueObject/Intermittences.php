<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

/**
 * @property Intermittence[] $values
 */
final class Intermittences
{
    public function __construct(public readonly array $values) {}

    public static function create(Intermittence ...$values): self
    {
        Assert::eq(count($values), count(ScenarioUsage::cases()));
        return new self($values);
    }

    public function find(ScenarioUsage $scenario): Intermittence
    {
        return array_find(
            $this->values,
            fn(Intermittence $item): bool => $item->scenario === $scenario,
        );
    }

    public function i0(ScenarioUsage $scenario): float
    {
        return $this->find(scenario: $scenario)->i0;
    }

    public function int(ScenarioUsage $scenario): float
    {
        return $this->find(scenario: $scenario)->int;
    }

    /**
     * @return Intermittence[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
