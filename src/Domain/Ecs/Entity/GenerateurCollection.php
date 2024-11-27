<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Service\{MoteurPerformance, MoteurPerte};
use App\Domain\Ecs\ValueObject\PerteCollection;
use App\Domain\Simulation\Simulation;

/**
 * @var Generateur[] $elements
 */
final class GenerateurCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Generateur $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Generateur $item) => $item->reinitialise());
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Generateur $item) => $item->calcule_performance($moteur, $simulation));
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Generateur $item) => $item->calcule_pertes($moteur, $simulation));
    }

    public function find(Id $id): ?Generateur
    {
        return $this->findFirst(fn(mixed $key, Generateur $item): bool => $item->id()->compare($id));
    }

    public function find_generateur_mixte(Id $id): ?Generateur
    {
        return $this->findFirst(fn(mixed $key, Generateur $item): bool => $item->id()->compare($id) || $item->generateur_mixte_id()?->compare($id) ?? false);
    }

    public function pertes(): PerteCollection
    {
        return $this->reduce(fn(PerteCollection $collection, Generateur $item): PerteCollection => $collection->merge(
            $item->pertes_generation(),
            $item->pertes_stockage(),
        ), new PerteCollection());
    }
}
