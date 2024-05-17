<?php

namespace App\Domain\PlancherHaut;

use App\Domain\Common\ValueObject\Id;
use App\Domain\PlancherHaut\Enum\QualiteComposant;
use App\Domain\Simulation\SimulationEngine;

final class PlancherHautEngineCollection
{
    /** @var PlancherHautEngine[] */
    private array $collection = [];

    public function __construct(private PlancherHautEngine $engine)
    {
    }

    /**
     * ∑dp,porte - Somme des déperditions (W/K)
     */
    public function dp(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PlancherHautEngine $item): float => $carry += $item->dp(), 0);
    }

    /**
     * ∑sdep,porte - Somme des surface déperditives (m²)
     */
    public function sdep(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PlancherHautEngine $item): float => $carry += $item->sdep(), 0);
    }

    /**
     * μ∑u,porte - Coefficient de transmission thermique moyen (W/(m².K))
     */
    public function u(): float
    {
        $sdep = $this->sdep();
        $fn = fn (float $carry, PlancherHautEngine $item): float => $carry += $item->u() * ($item->sdep() / $sdep);
        return $sdep ? \array_reduce($this->collection, $fn, 0) : 0;
    }

    /**
     * Indicateur de performance moyen des planchers hauts
     */
    public function qualite_composant(): QualiteComposant|false
    {
        return ($u = $this->u()) ? QualiteComposant::from_uph($u) : false;
    }

    /**
     * @param Id $id - Identifiant du plancher haut
     */
    public function get(Id $id): ?PlancherHautEngine
    {
        $collection = \array_filter($this->collection, fn (PlancherHautEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return PlancherHautEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(PlancherHautCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (PlancherHaut $item): PlancherHautEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
