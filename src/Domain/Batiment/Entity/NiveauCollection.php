<?php

namespace App\Domain\Batiment\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Niveau[] $elements
 */
final class NiveauCollection extends ArrayCollection
{
    public function find(Id $id): ?Niveau
    {
        return $this->findFirst(fn (mixed $key, Niveau $item): bool => $item->id()->compare($id));
    }

    /**
     * Somme des surfaces habitables en m²
     */
    public function surface_habitable(): float
    {
        return $this->reduce(fn (float $carry, Niveau $item): float => $carry += $item->surface_habitable()->valeur(), 0);
    }

    /**
     * Somme des hauteurs sous plafonds en m
     */
    public function hauteur_sous_plafond(): float
    {
        return $this->reduce(fn (float $carry, Niveau $item): float => $carry += $item->hauteur_sous_plafond()->valeur(), 0);
    }

    /**
     * Hauteur sous plafonds moyenne pondéréee en m
     */
    public function hauteur_sous_plafond_moyenne(): float
    {
        return ($surface = $this->surface_habitable())
            ? $this->reduce(fn (float $carry, Niveau $item): float => $carry += $item->hauteur_sous_plafond()->valeur() * ($item->surface_habitable()->valeur() / $surface), 0)
            : 0;
    }
}
