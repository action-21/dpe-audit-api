<?php

namespace App\Domain\Chauffage\ValueObject;

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
        return new self($values);
    }

    public function find(ScenarioUsage $scenario): Rendement
    {
        return array_find(
            $this->values,
            fn(Rendement $item): bool => $item->scenario === $scenario,
        );
    }

    public function get(ScenarioUsage $scenario): float
    {
        return $this->find(scenario: $scenario)->rendement;
    }

    /**
     * @return Rendement[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
