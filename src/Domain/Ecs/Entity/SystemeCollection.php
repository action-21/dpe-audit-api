<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ecs\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerte, MoteurRendement};
use App\Domain\Ecs\ValueObject\PerteCollection;
use App\Domain\Simulation\Simulation;

/**
 * @var Systeme[] $elements
 */
final class SystemeCollection extends ArrayCollection
{
    public function controle(): void
    {
        $this->walk(fn(Systeme $item) => $item->controle());
    }

    public function reinitialise(): void
    {
        $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_dimensionnement($moteur));
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_pertes($moteur, $simulation));
    }

    public function calcule_rendement(MoteurRendement $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_rendement($moteur));
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_consommations($moteur));
    }

    public function find(Id $id): ?Systeme
    {
        return $this->findFirst(fn(mixed $key, Systeme $item): bool => $item->id()->compare($id));
    }

    public function has_generateur(Id $id): bool
    {
        return $this->exists(fn(mixed $key, Systeme $item): bool => $item->generateur()->id()->compare($id));
    }

    public function pertes(): PerteCollection
    {
        return $this->reduce(fn(PerteCollection $collection, Systeme $item): PerteCollection => $collection->merge(
            $item->pertes_stockage(),
            $item->pertes_distribution(),
        ), new PerteCollection());
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Systeme $item): ConsommationCollection => $collection->merge(
            $item->consommations(),
            $item->consommations_auxiliaires(),
        ), new ConsommationCollection());
    }
}
