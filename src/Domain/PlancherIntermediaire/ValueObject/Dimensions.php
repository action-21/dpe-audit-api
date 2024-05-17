<?php

namespace App\Domain\PlancherIntermediaire\ValueObject;

final class Dimensions
{
    public function __construct(
        public readonly Surface $surface,
        public readonly Lineaire $lineaire,
        public readonly Epaisseur $epaisseur,
    ) {
    }

    public static function from(float $surface, float $lineaire, float $epaisseur): self
    {
        return new self(
            surface: Surface::from($surface),
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
