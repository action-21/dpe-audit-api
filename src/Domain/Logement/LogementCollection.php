<?php

namespace App\Domain\Logement;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Logement[] $elements
 */
final class LogementCollection extends ArrayCollection
{
    public function find(Id $id): ?Logement
    {
        return $this->filter(fn (Logement $item): bool => $item->id()->compare($id))->first();
    }

    /**
     * Surface habitable totale en m²
     */
    public function surface_habitable(): float
    {
        return $this->reduce(fn (float $carry, Logement $item): float => $carry += $item->surface_habitable(), 0);
    }

    /**
     * Surface habitable moyenne en m²
     */
    public function surface_habitable_moyenne(): float
    {
        return $this->count() > 0 ? $this->surface_habitable() / $this->count() : 0;
    }

    /**
     * Hauteur sous plafond moyenne en m
     */
    public function hauteur_sous_plafond(): float
    {
        return ($count = $this->count() > 0)
            ? $this->reduce(fn (float $carry, Logement $item): float => $carry += $item->hauteur_sous_plafond() / $count, 0)
            : 0;
    }

    /**
     * Volume habitable en m3
     */
    public function volume_habitable(): float
    {
        return $this->surface_habitable() * $this->hauteur_sous_plafond();
    }

    /**
     * Somme des surfaces ventilées par les installations de ventilation en m²
     * 
     * @param Id|null $id - Identifiant de l'installation de ventilation
     */
    public function surface_ventilee(?Id $reference_id = null): float
    {
        return $this->reduce(fn (float $carry, Logement $item): float => $carry += $item->ventilation_collection()->surface($reference_id), 0);
    }

    /**
     * Somme des surfaces climatisées en m²
     * 
     * @param Id|null $id - Identifiant du générateur de climatisation
     */
    public function surface_climatisee(?Id $generateur_id = null): float
    {
        return $this->reduce(fn (float $carry, Logement $item): float => $carry += $item->climatisation_collection()->surface_climatisee(generateur_id: $generateur_id), 0);
    }

    /**
     * Somme des surfaces climatisées utiles en m²
     * 
     * @param Id|null $id - Identifiant du générateur de climatisation
     */
    public function surface_climatisee_utile(?Id $generateur_id = null): float
    {
        return $this->reduce(fn (float $carry, Logement $item): float => $carry += $item->climatisation_collection()->surface_utile(generateur_id: $generateur_id), 0);
    }
}
