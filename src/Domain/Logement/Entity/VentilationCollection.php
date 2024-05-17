<?php

namespace App\Domain\Logement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Ventilation[] $elements
 */
final class VentilationCollection extends ArrayCollection
{
    public function find(Id $id): ?Ventilation
    {
        return $this->findFirst(fn (mixed $key, Ventilation $item): bool => $item->id()->compare($id));
    }

    /**
     * Somme des surfaces ventilées par les installations de ventilation en m²
     * 
     * @param Id|null $id - Identifiant de l'installation de ventilation
     */
    public function surface(?Id $reference_id = null): float
    {
        return $this
            ->filter(fn (Ventilation $item): bool => null === $reference_id || $item->reference()->id()->compare($reference_id))
            ->reduce(fn (float $carry, Ventilation $item): float => $carry += $item->surface()->valeur(), 0);
    }
}
