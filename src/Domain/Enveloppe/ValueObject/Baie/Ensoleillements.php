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

    public function sw(): float
    {
        $sw = array_reduce($this->values, fn(float $sw, Ensoleillement $item) => $sw += $item->sw->value, 0);
        return $sw / count($this->values);
    }

    public function fe(): float
    {
        $fe = array_reduce($this->values, fn(float $fe, Ensoleillement $item) => $fe += $item->fe, 0);
        return $fe / count($this->values);
    }

    public function c1(): float
    {
        $c1 = array_reduce($this->values, fn(float $c1, Ensoleillement $item) => $c1 += $item->c1, 0);
        return $c1 / count($this->values);
    }

    public function sse(?Mois $mois = null): float
    {
        $values = $mois ? array_filter($this->values, fn(Ensoleillement $item) => $item->mois === $mois) : $this->values;
        return \array_reduce($values, fn(float $sse, Ensoleillement $item) => $sse += $item->sse, 0);
    }

    /**
     * @return Ensoleillement[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
