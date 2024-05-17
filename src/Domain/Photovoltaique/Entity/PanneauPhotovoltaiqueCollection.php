<?php

namespace App\Domain\Photovoltaique\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property PanneauPhotovoltaique[] $elements
 */
final class PanneauPhotovoltaiqueCollection extends ArrayCollection
{
    public function find(Id $id): ?PanneauPhotovoltaique
    {
        return $this->filter(fn (PanneauPhotovoltaique $panneau) => $panneau->id()->compare($id))->first();
    }

    /**
     * Somme des surfaces des capteurs en m²
     */
    public function surface_capteurs(): float
    {
        return $this->reduce(fn (float $carry, PanneauPhotovoltaique $panneau) => $carry + $panneau->surface_capteurs_defaut(), 0);
    }
}
