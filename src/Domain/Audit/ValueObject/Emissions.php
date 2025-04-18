<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;

/**
 * @property Emission[] $values
 */
final class Emissions
{
    public function __construct(public readonly array $values) {}

    public static function create(\Closure $callback): self
    {
        $values = [];

        foreach (ScenarioUsage::cases() as $scenario) {
            $values[] = Emission::create(
                scenario: $scenario,
                emission: $callback($scenario),
            );
        }
        return static::from(...$values);
    }

    public static function from(Emission ...$values): self
    {
        return new self($values);
    }

    public function get(ScenarioUsage $scenario): float
    {
        $values = array_filter($this->values, fn(Emission $item) => $item->scenario === $scenario);
        return array_reduce($values, fn(float $carry, Emission $item) => $carry + $item->emission, 0);
    }

    /**
     * @return Emission[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
