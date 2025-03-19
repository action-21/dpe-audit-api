<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Refroidissement\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};

/**
 * @property Installation[] $elements
 */
final class InstallationCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Installation $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_dimensionnement($moteur));
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_consommations($moteur));
    }

    public function find(Id $id): ?Installation
    {
        return $this->findFirst(fn(mixed $key, Installation $item): bool => $item->id()->compare($id));
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $surface, Installation $item): float => $surface += $item->surface());
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Installation $item): ConsommationCollection => $collection->merge(
            $item->systemes()->consommations(),
        ), new ConsommationCollection());
    }
}
