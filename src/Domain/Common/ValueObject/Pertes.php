<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, TypePerte, Usage};
use Webmozart\Assert\Assert;

/**
 * @property Perte[] $elements
 */
final class Pertes
{
    public function __construct(public readonly array $values,) {}

    public static function create(Usage $usage, TypePerte $type, \Closure $callback): self
    {
        $values = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            foreach (Mois::cases() as $mois) {
                $perte = $callback(scenario: $scenario, mois: $mois);
                Assert::greaterThanEq($perte, 0);

                $values[] = Perte::create(
                    usage: $usage,
                    scenario: $scenario,
                    type: $type,
                    mois: $mois,
                    perte: $perte,
                );
            }
        }
        return self::from(...$values);
    }

    public static function from(Perte ...$values): self
    {
        return new self($values);
    }

    public function merge(self $value): self
    {
        return self::from(...[...$this->values, ...$value->values]);
    }

    public function get(ScenarioUsage $scenario, ?Mois $mois = null, ?Usage $usage = null, ?TypePerte $type = null): float
    {
        $values = array_filter($this->values, fn(Perte $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Perte $item) => $item->mois === $mois) : $values;
        $values = $usage ? array_filter($values, fn(Perte $item) => $item->usage === $usage) : $values;
        $values = $type ? array_filter($values, fn(Perte $item) => $item->type === $type) : $values;
        return array_reduce($values, fn(float $carry, Perte $item) => $carry + $item->perte, 0);
    }

    /**
     * @return Perte[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
