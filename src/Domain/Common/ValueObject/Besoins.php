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
                $besoin = $callback(scenario: $scenario, mois: $mois);
                Assert::greaterThanEq($besoin, 0);

                $values[] = Besoin::create(
                    usage: $usage,
                    scenario: $scenario,
                    mois: $mois,
                    besoin: $besoin,
                );
            }
        }
        return self::from(...$values);
    }

    public static function from(Besoin ...$values): self
    {
        return new self($values);
    }

    public function get(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, ?Mois $mois = null, ?Usage $usage = null): float
    {
        $values = array_filter($this->values, fn(Besoin $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Besoin $item) => $item->mois === $mois) : $values;
        $values = $usage ? array_filter($values, fn(Besoin $item) => $item->usage === $usage) : $values;
        return array_reduce($values, fn(float $carry, Besoin $item) => $carry += $item->besoin, 0);
    }

    /**
     * @return Besoin[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
