<?php

namespace App\Domain\PlancherBas;

use App\Domain\Common\ValueObject\Id;
use App\Domain\PlancherBas\Enum\QualiteComposant;
use App\Domain\Simulation\SimulationEngine;

final class PlancherBasEngineCollection
{
    /** @var PlancherBasEngine[] */
    private array $collection = [];

    public function __construct(private PlancherBasEngine $engine)
    {
    }

    /**
     * ∑dp,porte - Somme des déperditions (W/K)
     */
    public function dp(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PlancherBasEngine $item): float => $carry += $item->dp(), 0);
    }

    /**
     * ∑sdep,porte - Somme des surface déperditives (m²)
     */
    public function sdep(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PlancherBasEngine $item): float => $carry += $item->sdep(), 0);
    }

    /**
     * μ∑u,porte - Coefficient de transmission thermique moyen (W/(m².K))
     */
    public function u(): float
    {
        $sdep = $this->sdep();
        $fn = fn (float $carry, PlancherBasEngine $item): float => $carry += $item->u() * ($item->sdep() / $sdep);
        return $sdep ? \array_reduce($this->collection, $fn, 0) : 0;
    }

    /**
     * Indicateur de performance moyen des planchers bas
     */
    public function qualite_composant(): QualiteComposant|false
    {
        return ($u = $this->u()) ? QualiteComposant::from_upb($u) : false;
    }

    /**
     * @param Id $id - Identifiant du plancher bas
     */
    public function get(Id $id): ?PlancherBasEngine
    {
        $collection = \array_filter($this->collection, fn (PlancherBasEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return PlancherBasEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(PlancherBasCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (PlancherBas $item): PlancherBasEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
