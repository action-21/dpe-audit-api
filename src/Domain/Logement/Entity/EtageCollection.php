<?php

namespace App\Domain\Logement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Etage[] $elements
 */
final class EtageCollection extends ArrayCollection
{
    public function find(Id $id): ?Etage
    {
        return $this->findFirst(fn (mixed $key, Etage $item): bool => $item->id()->compare($id));
    }

    /**
     * Retourne la surface habitable totale des étages en m²
     */
    public function surface_habitable(): float
    {
        return $this->reduce(fn (float $carry, Etage $item): float => $carry += $item->surface_habitable()->valeur(), 0);
    }

    /**
     * Retourne la hauteur sous plafond moyenne des étages en m
     */
    public function hauteur_sous_plafond(): float
    {
        $surface_habitable = $this->surface_habitable() ?? 1;
        return $this->reduce(fn (float $carry, Etage $etage): float => $carry += $etage->hauteur_sous_plafond()->valeur() * ($etage->surface_habitable()->valeur() / $surface_habitable), 0);
    }
}
