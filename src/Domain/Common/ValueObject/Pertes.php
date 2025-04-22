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
                $values[] = Perte::create(
                    usage: $usage,
                    scenario: $scenario,
                    type: $type,
                    mois: $mois,
                    perte: $callback(scenario: $scenario, mois: $mois),
                );
            }
        }
        return self::from(...$values);
    }

    public static function from(Perte ...$values): self
    {
        Assert::lessThanEq(
            count($values),
            count(ScenarioUsage::cases()) * count(Usage::cases()) * count(Mois::cases()) * count(TypePerte::cases())
        );
        Assert::uniqueValues(array_map(
            fn(Perte $value) => "{$value->scenario->id()}{$value->usage->id()}{$value->mois->id()}{$value->type->id()}",
            $values
        ));

        return new self($values);
    }

    public function add(Perte $value): self
    {
        $values = [Perte::create(
            scenario: $value->scenario,
            usage: $value->usage,
            type: $value->type,
            mois: $value->mois,
            perte: $value->perte + $this->get(
                scenario: $value->scenario,
                usage: $value->usage,
                mois: $value->mois,
                type: $value->type,
            ),
        )];

        foreach ($this->values as $item) {
            if ($item->scenario === $value->scenario && $item->usage === $value->usage && $item->mois === $value->mois && $item->type === $value->type) {
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
        ?Mois $mois = null,
        ?Usage $usage = null,
        ?TypePerte $type = null,
    ): float {
        $values = array_filter($this->values, fn(Perte $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Perte $item) => $item->mois === $mois) : $values;
        $values = $usage ? array_filter($values, fn(Perte $item) => $item->usage === $usage) : $values;
        $values = $type ? array_filter($values, fn(Perte $item) => $item->type === $type) : $values;
        return array_reduce($values, fn(float $carry, Perte $item) => $carry + $item->perte, 0);
    }

    /**
     * @return Usage[]
     */
    public function usages(): array
    {
        return array_map(
            fn (string $usage) => Usage::from($usage),
            array_unique(array_map(fn(Perte $value) => $value->usage->id(), $this->values))
        );
    }

    /**
     * @return ScenarioUsage[]
     */
    public function scenarios(): array
    {
        return array_map(
            fn (string $scenario) => ScenarioUsage::from($scenario),
            array_unique(array_map(fn(Perte $value) => $value->scenario->id(), $this->values)),
        );
    }

    /**
     * @return TypePerte[]
     */
    public function types(): array
    {
        return array_map(
            fn (string $type) => TypePerte::from($type),
            array_unique(array_map(fn(Perte $value) => $value->type->id(), $this->values)),
        );
    }

    /**
     * @return Perte[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
