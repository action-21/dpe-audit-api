<?php

namespace App\Domain\Porte;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Porte\Enum\QualiteComposant;
use App\Domain\Simulation\SimulationEngine;

final class PorteEngineCollection
{
    /** @var PorteEngine[] */
    private array $collection = [];

    public function __construct(private PorteEngine $engine)
    {
    }

    /**
     * ∑dp,porte - Somme des déperditions (W/K)
     */
    public function dp(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PorteEngine $item): float => $carry += $item->dp(), 0);
    }

    /**
     * ∑sdep,porte - Somme des surface déperditives (m²)
     */
    public function sdep(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PorteEngine $item): float => $carry += $item->sdep(), 0);
    }

    /**
     * μ∑u,porte - Coefficient de transmission thermique moyen (W/(m².K))
     */
    public function u(): float
    {
        $sdep = $this->sdep();
        $fn = fn (float $carry, PorteEngine $item): float => $carry += $item->u() * ($item->sdep() / $sdep);
        return $sdep ? \array_reduce($this->collection, $fn, 0) : 0;
    }

    /**
     * Indicateur de performance moyen des portes
     */
    public function qualite_composant(): QualiteComposant|false
    {
        return ($u = $this->u()) ? QualiteComposant::from_uporte($u) : false;
    }

    /**
     * @param Id $id - Identifiant de la porte
     */
    public function get(Id $id): ?PorteEngine
    {
        $collection = \array_filter($this->collection, fn (PorteEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return PorteEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(PorteCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (Porte $item): PorteEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
