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
        Assert::eq(count(Mois::cases()) * count(ScenarioUsage::cases()), count($values));

        foreach (ScenarioUsage::cases() as $scenario) {
            foreach (Mois::cases() as $mois) {
                Assert::notNull(array_find(
                    $values,
                    fn(Apport $item) => $item->scenario === $scenario && $item->mois === $mois
                ));
            }
        }
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
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $apports, Apport $item) => $apports + $item->apport(), 0);
    }

    public function apports_fr(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, ?Mois $mois = null): float
    {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $apports, Apport $item) => $apports + $item->apport_fr(), 0);
    }

    public function sse(ScenarioUsage $scenario = ScenarioUsage::CONVENTIONNEL, ?Mois $mois = null): float
    {
        $values = array_filter($this->values, fn(Apport $item) => $item->scenario === $scenario);
        $values = $mois ? array_filter($values, fn(Apport $item) => $item->mois === $mois) : $values;
        return array_reduce($values, fn(float $sse, Apport $item) => $sse + $item->sse, 0);
    }
}
