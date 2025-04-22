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
        return self::from(...ScenarioUsage::each(fn(ScenarioUsage $scenario) => Consommation::create(
            usage: $usage,
            scenario: $scenario,
            energie: $energie,
            consommation: $callback(scenario: $scenario),
        )));
    }

    public static function from(Consommation ...$values): self
    {
        Assert::lessThanEq(
            count($values),
            count(ScenarioUsage::cases()) * count(Usage::cases()) * count(Energie::cases())
        );
        Assert::uniqueValues(array_map(
            fn(Consommation $value) => "{$value->scenario->id()}{$value->usage->id()}{$value->energie->id()}",
            $values
        ));

        return new self($values);
    }

    public function add(Consommation $value): self
    {
        $values = [Consommation::create(
            scenario: $value->scenario,
            usage: $value->usage,
            energie: $value->energie,
            consommation: $value->consommation_ef + $this->get(
                scenario: $value->scenario,
                usage: $value->usage,
                energie: $value->energie,
            ),
        )];

        foreach ($this->values as $item) {
            if ($item->scenario === $value->scenario && $item->usage === $value->usage && $item->energie === $value->energie) {
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
     * @return Consommation[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
