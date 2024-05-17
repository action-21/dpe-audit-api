<?php

namespace App\Domain\Refend\ValueObject;

/**
 * Dimensions du refend
 */
final class Dimensions
{
    public function __construct(
        public readonly Lineaire $lineaire,
        public readonly Epaisseur $epaisseur,
    ) {
    }

    public static function from(float $lineaire, float $epaisseur): self
    {
        return new self(
            lineaire: Lineaire::from($lineaire),
            epaisseur: Epaisseur::from($epaisseur),
        );
    }

    /**
     * Emprise du refend en m²
     */
    public function emprise(): float
    {
        return $this->epaisseur->valeur() / 100 * $this->lineaire->valeur();
    }
}
