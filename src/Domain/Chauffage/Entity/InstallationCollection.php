<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Service\{MoteurConsommation, MoteurDimensionnement, MoteurRendement};
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Simulation\Simulation;

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

    public function calcule_rendement(MoteurRendement $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_rendement($moteur, $simulation));
    }

    public function calcule_consommations(MoteurConsommation $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_consommations($moteur, $simulation));
    }

    public function find(Id $id): ?Installation
    {
        return $this->findFirst(fn(mixed $key, Installation $item): bool => $item->id()->compare($id));
    }

    public function filter_by_effet_joule(): self
    {
        return $this->filter(fn(Installation $installation) => $installation->effet_joule());
    }

    public function search_systemes_by_generateur(Id $id): SystemeCollection
    {
        $collection = new SystemeCollection;
        foreach ($this->elements as $item) {
            $collection = $collection->merge($collection, $item->systemes()->filter_by_generateur($id));
        }
        return $collection;
    }

    public function has_generateur(Id $id): bool
    {
        return $this->search_systemes_by_generateur($id)->count() > 0;
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Installation $installation) => $carry += $installation->surface());
    }

    public function effet_joule(): bool
    {
        return $this->filter_by_effet_joule()->surface() > $this->surface() / 2;
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Installation $item): ConsommationCollection => $collection->merge(
            $item->systemes()->consommations(),
        ), new ConsommationCollection());
    }
}
