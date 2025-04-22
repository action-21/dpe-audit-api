<?php

namespace App\Domain\Enveloppe\ValueObject\Lnc;

use App\Domain\Common\Enum\Mois;

/**
 * @property EnsoleillementBaie[] $values
 */
final class EnsoleillementsBaie
{
    public function __construct(public readonly array $values) {}

    public static function create(EnsoleillementBaie ...$values): self
    {
        return new self($values);
    }

    public function t(?Mois $mois = null): float
    {
        $values = $mois ? array_filter($this->values, fn(EnsoleillementBaie $item) => $item->mois === $mois) : $this->values;
        $t = array_reduce($values, fn(float $t, EnsoleillementBaie $item) => $t += $item->t, 0);
        return $t / count($values);
    }

    public function fe(): float
    {
        $fe = array_reduce($this->values, fn(float $fe, EnsoleillementBaie $item) => $fe += $item->fe, 0);
        return $fe / count($this->values);
    }

    public function c1(): float
    {
        $c1 = array_reduce($this->values, fn(float $c1, EnsoleillementBaie $item) => $c1 += $item->c1, 0);
        return $c1 / count($this->values);
    }

    public function sst(?Mois $mois = null): float
    {
        $values = $mois ? array_filter($this->values, fn(EnsoleillementBaie $item) => $item->mois === $mois) : $this->values;
        return \array_reduce($values, fn(float $sst, EnsoleillementBaie $item) => $sst += $item->sst, 0);
    }

    /**
     * @return EnsoleillementBaie[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
