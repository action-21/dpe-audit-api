<?php

namespace App\Domain\Mur;

use App\Domain\Mur\Enum\QualiteComposant;
use App\Domain\Simulation\SimulationEngine;

final class MurEngineCollection
{
    /** @var MurEngine[] */
    private array $collection = [];

    public function __construct(private MurEngine $engine)
    {
    }

    /**
     * ∑dp,porte - Somme des déperditions (W/K)
     */
    public function dp(): float
    {
        return \array_reduce($this->collection, fn (float $carry, MurEngine $item): float => $carry += $item->dp(), 0);
    }

    /**
     * ∑sdep,porte - Somme des surface déperditives (m²)
     */
    public function sdep(): float
    {
        return \array_reduce($this->collection, fn (float $carry, MurEngine $item): float => $carry += $item->sdep(), 0);
    }

    /**
     * μ∑u,porte - Coefficient de transmission thermique moyen (W/(m².K))
     */
    public function u(): float
    {
        $sdep = $this->sdep();
        $fn = fn (float $carry, MurEngine $item): float => $carry += $item->u() * ($item->sdep() / $sdep);
        return $sdep ? \array_reduce($this->collection, $fn, 0) : 0;
    }

    /**
     * Indicateur de performance moyen des murs
     */
    public function qualite_composant(): QualiteComposant|false
    {
        return ($u = $this->u()) ? QualiteComposant::from_umur($u) : false;
    }

    public function get(Mur $entity): ?MurEngine
    {
        $collection = \array_filter($this->collection, fn (MurEngine $item): bool => $item->input()->id()->compare($entity->id()));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return MurEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(MurCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (Mur $item): MurEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
