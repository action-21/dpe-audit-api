<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ecs\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerte, MoteurRendement};
use App\Domain\Ecs\ValueObject\PerteCollection;
use App\Domain\Simulation\Simulation;

/**
 * @var Installation[] $elements
 */
final class InstallationCollection extends ArrayCollection
{
    public function controle(): void
    {
        $this->walk(fn(Installation $item) => $item->controle());
    }

    public function reinitialise(): void
    {
        $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_dimensionnement($moteur));
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_pertes($moteur, $simulation));
    }

    public function calcule_rendement(MoteurRendement $moteur): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_rendement($moteur));
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
        return $this->reduce(fn(float $carry, Installation $item): float => $carry += $item->surface());
    }

    public function has_generateur(Id $id): bool
    {
        return $this->exists(fn(mixed $key, Installation $item): bool => $item->systemes()->has_generateur($id));
    }

    public function pertes(): PerteCollection
    {
        return $this->reduce(fn(PerteCollection $collection, Installation $item): PerteCollection => $collection->merge(
            $item->systemes()->pertes(),
        ), new PerteCollection());
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Installation $item): ConsommationCollection => $collection->merge(
            $item->systemes()->consommations(),
        ), new ConsommationCollection());
    }
}
