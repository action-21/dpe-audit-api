<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\LienGenerationEmission;
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Emission[] $elements
 */
final class EmissionCollection extends ArrayCollection
{
    public function find(Id $id): ?Emission
    {
        return $this->filter(fn (Emission $emetteur) => $emetteur->id()->compare($id))->first();
    }

    public function find_by_lien_generation_emission(LienGenerationEmission $enum): ?Emission
    {
        return $this->filter(fn (Emission $emetteur) => $emetteur->lien_generation_emission() === $enum)->first();
    }

    /**
     * Retourne la somme des surfaces des émetteurs en m²
     */
    public function surface(): float
    {
        return $this->reduce(fn (float $carry, Emission $emetteur) => $carry += $emetteur->surface()->valeur(), 0);
    }
}
