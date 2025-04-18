<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Enum\ScenarioUsage;
use Webmozart\Assert\Assert;

/**
 * @property Rendement[] $values
 */
final class Rendements
{
    public function __construct(public readonly array $values) {}

    public static function create(Rendement ...$values): self
    {
        Assert::eq(count($values), count(ScenarioUsage::cases()));

        foreach (ScenarioUsage::cases() as $scenario) {
            Assert::notNull(array_find($values, fn(Rendement $item) => $item->scenario === $scenario));
        }
        return new self($values);
    }

    public function find(ScenarioUsage $scenario): Rendement
    {
        return array_find($this->values, fn(Rendement $item) => $item->scenario === $scenario);
    }

    public function iecs(ScenarioUsage $scenario): float
    {
        return $this->find(scenario: $scenario)->iecs;
    }

    /**
     * @return array|Rendement[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
