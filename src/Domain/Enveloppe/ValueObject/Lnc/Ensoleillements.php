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

    public function find(Mois $mois): Ensoleillement
    {
        return array_find(
            $this->values,
            fn(Ensoleillement $ensoleillement) => $ensoleillement->mois === $mois,
        );
    }

    public function t(Mois $mois): float
    {
        return $this->find($mois)->t;
    }

    public function sse(Mois $mois): float
    {
        return $this->find($mois)->sse;
    }
}
