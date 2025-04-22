<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use Webmozart\Assert\Assert;

/**
 * @property Besoin[] $values
 */
final class Besoins
{
    public function __construct(public readonly array $values) {}

    public static function create(Usage $usage, \Closure $callback): self
    {
        $values = [];
        foreach (ScenarioUsage::cases() as $scenario) {
            foreach (Mois::cases() as $mois) {
                $values[] = Besoin::create(
                    usage: $usage,
                    scenario: $scenario,
                    mois: $mois,
                    besoin: $callback(scenario: $scenario, mois: $mois),
                );
            }
        }
        return self::from(...$values);
    }

    public static function from(Besoin ...$values): self
    {
        Assert::lessThanEq(
            count($values),
            count(ScenarioUsage::cases()) * count(Usage::cases()) * count(Mois::cases())
        );
        Assert::uniqueValues(array_map(
            fn(Besoin $value) => "{$value->scenario->id()}{$value->usage->id()}{$value->mois->id()}",
            $values
        ));

        return new self($values);
    }

    public function get(
        ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL,
        ?Mois $mois = null,
        ?Usage $usage = null,
    ): float {
        $values = array_filter($this->values, fn(Besoin $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Besoin $item) => $item->mois === $mois) : $values;
        $values = $usage ? array_filter($values, fn(Besoin $item) => $item->usage === $usage) : $values;
        return array_reduce($values, fn(float $carry, Besoin $item) => $carry += $item->besoin, 0);
    }

    /**
     * @return Usage[]
     */
    public function usages(): array
    {
        return array_map(
            fn (string $usage) => Usage::from($usage),
            array_unique(array_map(fn(Besoin $value) => $value->usage->id(), $this->values))
        );
    }

    /**
     * @return ScenarioUsage[]
     */
    public function scenarios(): array
    {
        return array_map(
            fn (string $scenario) => ScenarioUsage::from($scenario),
            array_unique(array_map(fn(Besoin $value) => $value->scenario->id(), $this->values)),
        );
    }

    /**
     * @return Besoin[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
