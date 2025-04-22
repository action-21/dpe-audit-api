<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use Webmozart\Assert\Assert;

/**
 * @property Apport[] $values
 */
final class Apports
{
    public function __construct(public readonly array $values) {}

    public static function create(Apport ...$values): self
    {
        return self::from(...$values);
    }

    public static function from(Apport ...$values): self
    {
        Assert::eq(count(Mois::cases()) * count(ScenarioUsage::cases()), count($values));
        Assert::uniqueValues(array_map(
            fn(Apport $value) => "{$value->scenario->id()}{$value->mois->id()}",
            $values,
        ));

        return new self($values);
    }

    public function f(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, ?Mois $mois = null): float
    {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);

        if ($mois) {
            return array_find($values, fn(Apport $item) => $item->mois === $mois)->f;
        }
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        $f = array_reduce($values, fn(float $f, Apport $item) => $f + $item->f * $item->mois->nj(), 0);
        return $f / Mois::reduce(fn(int $carry, Mois $item) => $carry += $item->nj());
    }

    public function apports(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, ?Mois $mois = null): float
    {
        return $this->apports_internes(scenario: $scenario, mois: $mois)
            + $this->apports_solaires(scenario: $scenario, mois: $mois);
    }

    public function apports_internes(
        ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL,
        ?Mois $mois = null,
    ): float {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $apports, Apport $item) => $apports + $item->apport_interne, 0);
    }

    public function apports_solaires(
        ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL,
        ?Mois $mois = null,
    ): float {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $apports, Apport $item) => $apports + $item->apport_solaire, 0);
    }

    public function apports_fr(
        ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL,
        ?Mois $mois = null,
    ): float {
        return $this->apports_internes_fr(scenario: $scenario, mois: $mois)
            + $this->apports_solaires_fr(scenario: $scenario, mois: $mois);
    }

    public function apports_internes_fr(
        ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL,
        ?Mois $mois = null,
    ): float {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $apports, Apport $item) => $apports + $item->apport_interne_fr, 0);
    }

    public function apports_solaires_fr(
        ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL,
        ?Mois $mois = null,
    ): float {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $apports, Apport $item) => $apports + $item->apport_solaire_fr, 0);
    }

    public function sse(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, ?Mois $mois = null): float
    {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $sse, Apport $item) => $sse + $item->sse, 0);
    }

    /**
     * @return ScenarioUsage[]
     */
    public function scenarios(): array
    {
        return array_map(
            fn(string $scenario) => ScenarioUsage::from($scenario),
            array_unique(array_map(fn(Apport $value) => $value->scenario->id(), $this->values)),
        );
    }

    /**
     * @return Apport[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
