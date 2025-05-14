<?php

namespace App\Domain\Enveloppe\ValueObject\Lnc;

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

    public function t(?Mois $mois = null): float
    {
        $values = $mois ? array_filter($this->values, fn(Ensoleillement $item) => $item->mois === $mois) : $this->values;
        $t = array_reduce($values, fn(float $t, Ensoleillement $item) => $t += $item->t, 0);
        return $t / count($values);
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

    public function sst(?Mois $mois = null): float
    {
        $values = $mois ? array_filter($this->values, fn(Ensoleillement $item) => $item->mois === $mois) : $this->values;
        return \array_reduce($values, fn(float $sst, Ensoleillement $item) => $sst += $item->sst, 0);
    }

    public function ssd(?Mois $mois = null): float
    {
        $values = $mois ? array_filter($this->values, fn(Ensoleillement $item) => $item->mois === $mois) : $this->values;
        return \array_reduce($values, fn(float $ssd, Ensoleillement $item) => $ssd += $item->ssd, 0);
    }

    public function ssind(?Mois $mois = null): float
    {
        $values = $mois ? array_filter($this->values, fn(Ensoleillement $item) => $item->mois === $mois) : $this->values;
        return \array_reduce($values, fn(float $ssind, Ensoleillement $item) => $ssind += $item->ssind, 0);
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
