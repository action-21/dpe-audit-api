<?php

namespace App\Domain\Paroi;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Lnc;

/**
 * @property Paroi[] $elements
 */
class ParoiCollection extends ArrayCollection
{
    public function find(Id $id): ?Paroi
    {
        return $this->findFirst(fn (mixed $key, Paroi $item): bool => $item->id()->compare($id));
    }

    public function find_by_local_non_chauffe(Lnc $entity): ?Paroi
    {
        return $this->findFirst(fn (mixed $key, Paroi $item): bool => $item->local_non_chauffe()?->id()->compare($entity->id()));
    }

    /**
     * Retourne une collection de parois correspondant au Local Non Chauffé en paramètre
     */
    public function search_by_local_non_chauffe(Lnc $entity): self
    {
        return $this->filter(fn (Paroi $item): bool => $item->local_non_chauffe()?->id()->compare($entity->id()));
    }

    /**
     * Retourne une collection filtrée par type de paroi
     */
    public function search_without_type(TypeParoi $type_paroi): self
    {
        return $this->filter(fn (Paroi $item): bool => $item->type_paroi() !== $type_paroi);
    }

    /**
     * Retourne une collection filtrée par type de paroi
     */
    public function search_by_type(TypeParoi $type_paroi): self
    {
        return $this->filter(fn (Paroi $item): bool => $item->type_paroi() === $type_paroi);
    }

    /**
     * Retourne une collection de paroi opaque
     */
    public function search_paroi_opaque(): ParoiOpaqueCollection
    {
        return new ParoiOpaqueCollection(
            $this->filter(fn (Paroi $item): bool => $item->type_paroi()->paroi_opaque())->to_array()
        );
    }

    /**
     * Retourne une collection d'ouverture
     */
    public function search_ouverture(): OuvertureCollection
    {
        return new OuvertureCollection(
            $this->filter(fn (Paroi $item): bool => $item->type_paroi()->ouverture())->to_array()
        );
    }

    /**
     * Somme des surfaces déperties des parois de la collection en m²
     */
    public function surface_deperditive(): float
    {
        return $this->reduce(fn (float $carry, Paroi $item): float => $carry += $item->surface_deperditive(), 0);
    }

    /**
     * État d'isolation majoritaire des parois de la collection
     */
    public function est_isole(): bool
    {
        return $this->filter(fn (Paroi $item): bool => $item->est_isole())->surface_deperditive() > $this->surface_deperditive() / 2;
    }
}
