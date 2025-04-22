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
        return self::from(...ScenarioUsage::each(fn(ScenarioUsage $scenario) => Emission::create(
            usage: $usage,
            scenario: $scenario,
            emission: $callback(scenario: $scenario),
        )));
    }

    public static function from(Emission ...$values): self
    {
        Assert::lessThanEq(count($values), count(ScenarioUsage::cases()) * count(Usage::cases()));
        Assert::uniqueValues(array_map(
            fn(Emission $value) => "{$value->usage->id()}{$value->scenario->id()}",
            $values,
        ));
        return new self($values);
    }

    public function add(Emission $value): self
    {
        $values = [Emission::create(
            scenario: $value->scenario,
            usage: $value->usage,
            emission: $value->emission + $this->get(scenario: $value->scenario, usage: $value->usage),
        )];

        foreach ($this->values as $item) {
            if ($item->scenario === $value->scenario && $item->usage === $value->usage) {
                continue;
            }
            $values[] = $item;
        }
        return static::from(...$values);
    }

    public function merge(self $value): self
    {
        foreach ($this->values as $item) {
            $value = $value->add($item);
        }
        return $value;
    }

    public function get(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, ?Usage $usage = null): float
    {
        $values = array_filter($this->values, fn(Emission $item) => $item->scenario === $scenario);
        $values = $usage ? array_filter($values, fn(Emission $item) => $item->usage === $usage) : $values;
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
