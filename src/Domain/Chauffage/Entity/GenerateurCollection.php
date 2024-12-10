<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Chauffage\Service\{MoteurDimensionnement, MoteurPerformance, MoteurPerte};
use App\Domain\Chauffage\ValueObject\PerteCollection;
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Simulation\Simulation;

/**
 * @property Generateur[] $elements
 */
final class GenerateurCollection extends ArrayCollection
{
    public function controle(): void
    {
        $this->walk(fn(Generateur $item) => $item->controle());
    }

    public function reinitialise(): void
    {
        $this->walk(fn(Emetteur $item) => $item->reinitialise());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Generateur $item) => $item->calcule_dimensionnement($moteur, $simulation));
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

    public function filter_by_type(TypeGenerateur $type): self
    {
        return $this->filter(fn(Generateur $generateur) => $generateur->signaletique()->type === $type);
    }

    public function pertes(): PerteCollection
    {
        return $this->reduce(fn(PerteCollection $collection, Generateur $item): PerteCollection => $collection->merge(
            $item->pertes_generation(),
        ), new PerteCollection());
    }
}
