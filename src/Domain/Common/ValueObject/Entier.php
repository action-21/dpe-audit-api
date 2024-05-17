<?php

namespace App\Domain\Common\ValueObject;

abstract class Entier
{
    public function __construct(public readonly int $valeur)
    {
    }

    protected static function _from(float|int $valeur, bool $positive = true, ?int $min = null, ?int $max = null): static
    {
        if ($positive && $valeur <= 0) {
            throw new \InvalidArgumentException("La valeur renseignée doit être positive");
        }
        if (null !== $min && $valeur < $min) {
            throw new \InvalidArgumentException("La valeur renseignée doit être supérieure à {$min}");
        }
        if (null !== $max && $valeur > $max) {
            throw new \InvalidArgumentException("La valeur renseignée doit être inférieure à {$max}");
        }
        return new static((int) $valeur);
    }

    public function valeur(): int
    {
        return $this->valeur;
    }
}
