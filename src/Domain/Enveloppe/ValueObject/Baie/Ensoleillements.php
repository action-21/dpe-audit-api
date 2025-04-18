<?php

namespace App\Domain\Enveloppe\ValueObject\Baie;

use App\Domain\Common\Enum\Mois;

/**
 * @property Ensoleillement[] $values
 */
final class Ensoleillements
{
    public function __construct(public readonly array $values) {}

    public static function create(Ensoleillement ...$values): self
    {
        return new self($values);
    }

    public function sse(Mois $mois): float
    {
        $values = array_filter($this->values, fn(Ensoleillement $item) => $item->mois === $mois);
        return \array_reduce($values, fn(float $sse, Ensoleillement $item) => $sse += $item->sse, 0);
    }
}
