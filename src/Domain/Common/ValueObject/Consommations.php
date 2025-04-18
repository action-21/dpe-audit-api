<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Energie, ScenarioUsage, Usage};
use Webmozart\Assert\Assert;

/**
 * @property Consommation[] $values
 */
final class Consommations
{
    public function __construct(public readonly array $values) {}

    public static function create(Usage $usage, Energie $energie, \Closure $callback): self
    {
        $values = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            $consommation = $callback(scenario: $scenario);
            Assert::greaterThanEq($consommation, 0);

            $values[] = Consommation::create(
                usage: $usage,
                scenario: $scenario,
                energie: $energie,
                consommation: $consommation,
            );
        }
        return self::from(...$values);
    }

    public static function from(Consommation ...$values): self
    {
        return new self($values);
    }

    public function merge(self $value): self
    {
        return static::from(...[...$this->values, ...$value->values]);
    }

    public function get(
        ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL,
        ?Usage $usage = null,
        ?Energie $energie = null,
        ?bool $energie_primaire = null,
    ): float {
        $values = array_filter($this->values, fn(Consommation $item) => $item->scenario === $scenario);
        $values = $usage ? array_filter($values, fn(Consommation $item) => $item->usage === $usage) : $values;
        $values = $energie ? array_filter($values, fn(Consommation $item) => $item->energie === $energie) : $values;
        return array_reduce($values, fn(float $carry, Consommation $item) => $carry + ($energie_primaire ? $item->consommation_ep : $item->consommation_ef), 0);
    }

    /**
     * @return Usage[]
     */
    public function usages(): array
    {
        return array_unique(array_map(fn(Consommation $item) => $item->usage, $this->values));
    }

    /**
     * @return Consommation[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
