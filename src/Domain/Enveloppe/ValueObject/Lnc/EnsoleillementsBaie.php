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

    public function find(Mois $mois): EnsoleillementBaie
    {
        return array_find(
            $this->values,
            fn(EnsoleillementBaie $ensoleillement) => $ensoleillement->mois === $mois,
        );
    }

    public function t(Mois $mois): float
    {
        return $this->find($mois)->t;
    }

    public function sst(Mois $mois): float
    {
        return $this->find($mois)->sst;
    }
}
