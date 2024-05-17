<?php

namespace App\Domain\Common\ValueObject;

abstract class Nombre
{
    public function __construct(public readonly float $valeur)
    {
    }

    protected static function _from(float $valeur, bool $positive = true, ?float $min = null, ?float $max = null): static
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
        return new static($valeur);
    }

    public function valeur(): float
    {
        return $this->valeur;
    }
}
