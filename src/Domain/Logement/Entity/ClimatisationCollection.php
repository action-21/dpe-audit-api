<?php

namespace App\Domain\Logement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Climatisation[] $elements
 */
final class ClimatisationCollection extends ArrayCollection
{
    public function find(Id $id): ?Climatisation
    {
        return $this->findFirst(fn (mixed $key, Climatisation $item): bool => $item->id()->compare($id));
    }

    /**
     * Somme des surfaces climatisées en m²
     * 
     * @param Id|null $id - Identifiant du générateur de climatisation
     */
    public function surface_climatisee(?Id $generateur_id = null): float
    {
        return $this
            ->filter(fn (Climatisation $item): bool => null === $generateur_id || null !== $item->generateur_collection()->find(id: $generateur_id))
            ->reduce(fn (float $carry, Climatisation $item): float => $carry += $item->surface()->valeur(), 0);
    }

    /**
     * Somme des surfaces utiles des générateurs de climatisation en m²
     */
    public function surface_utile(?Id $generateur_id = null): float
    {
        return $this
            ->filter(fn (Climatisation $item): bool => null === $generateur_id || null !== $item->generateur_collection()->find(id: $generateur_id))
            ->reduce(fn (float $carry, Climatisation $item): float => $carry += $item->surface()->valeur() * $item->generateur_collection()->count(), 0);
    }
}
