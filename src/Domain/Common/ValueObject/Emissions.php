<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use Webmozart\Assert\Assert;

/**
 * @property Emission[] $values
 */
final class Emissions
{
    public function __construct(public readonly array $values) {}

    public static function create(Usage $usage, \Closure $callback): self
    {
        $values = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            $emission = $callback(scenario: $scenario);
            Assert::greaterThanEq($emission, 0);

            $values[] = Emission::create(
                usage: $usage,
                scenario: $scenario,
                emission: $emission,
            );
        }
        return self::from(...$values);
    }

    public static function from(Emission ...$values): self
    {
        return new self($values);
    }

    public function merge(self $value): self
    {
        return self::from(...[...$this->values, ...$value->values]);
    }

    public function find(ScenarioUsage $scenario): ?Emission
    {
        return array_find($this->values, fn(Emission $item) => $item->scenario === $scenario);
    }

    public function get(ScenarioUsage $scenario, ?Usage $usage = null): float
    {
        $values = array_filter($this->values, fn(Emission $item) => $item->scenario === $scenario);
        $values = $usage ? array_filter($values, fn(Emission $item) => $item->usage === $usage) : $values;
        return array_reduce($values, fn(float $carry, Emission $item) => $carry + $item->emission, 0);
    }

    /**
     * @return Usage[]
     */
    public function usages(): array
    {
        return array_unique(array_map(fn(Emission $item) => $item->usage, $this->values));
    }

    /**
     * @return Emission[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
