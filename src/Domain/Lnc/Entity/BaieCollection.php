<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Baie[] $elements
 */
final class BaieCollection extends ParoiCollection
{
    public function find(Id $id): ?Baie
    {
        return $this->findFirst(fn (mixed $key, Baie $item): bool => $item->id()->compare($id));
    }

    /**
     * Recherche les baies prinipales de l'espace tampon solarisé
     */
    public function search_by_orientation(): self
    {
        /** @var Orientation[] */
        $orientations = [];
        $max = 0;

        foreach (Orientation::cases() as $orientation) {
            $surface = $this
                ->filter(fn (Baie $item): bool => false === $item->orientation()->valeur() > 0 && $item->orientation()->enum()->point_cardinal() === $orientation->point_cardinal())
                ->reduce(fn (float $carry, Baie $item): float => $carry += $item->surface()->valeur(), 0);

            if ($surface >= $max) {
                $max = $surface;
                $orientations[] = $orientation;
            }
        }

        return $this->filter(fn (Baie $item): bool => \in_array($item->orientation(), $orientations));
    }

    /**
     * Retourne les orientations majoritaires d'une collection de baies
     * 
     * @return Orientation[]
     */
    public function orientations(): array
    {
        return \array_unique(\array_map(
            fn (Baie $item): Orientation => $item->orientation()->enum(),
            $this->search_by_orientation()->values()
        ));
    }
}
