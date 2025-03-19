<?php

namespace App\Domain\Production\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\Service\MoteurProduction;
use App\Domain\Production\ValueObject\ProductionPhotovoltaiqueCollection;
use App\Domain\Simulation\Simulation;

/**
 * @property PanneauPhotovoltaique[] $elements
 */
final class PanneauPhotovoltaiqueCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(PanneauPhotovoltaique $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(PanneauPhotovoltaique $item) => $item->reinitialise());
    }

    public function calcule_production(MoteurProduction $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(PanneauPhotovoltaique $item) => $item->calcule_production($moteur, $simulation));
    }

    public function find(Id $id): ?PanneauPhotovoltaique
    {
        return $this->findFirst(fn(mixed $key, PanneauPhotovoltaique $item): bool => $item->id()->compare($id));
    }

    public function productions(): ProductionPhotovoltaiqueCollection
    {
        return $this->reduce(fn(ProductionPhotovoltaiqueCollection $collection, PanneauPhotovoltaique $item): ProductionPhotovoltaiqueCollection => $collection->merge(
            $item->productions(),
        ), new ProductionPhotovoltaiqueCollection());
    }
}
