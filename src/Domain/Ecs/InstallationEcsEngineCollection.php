<?php

namespace App\Domain\Ecs;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class InstallationEcsEngineCollection
{
    /** @var InstallationEcsEngine[] */
    private array $collection;

    public function __construct(private InstallationEcsEngine $engine)
    {
    }

    /**
     * Consommation annuelle des générateurs en kWh PCI
     */
    public function cecs(bool $scenario_depensier = false): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationEcsEngine $item): float => $carry += $item->cecs($scenario_depensier), 0);
    }

    /**
     * Consommation des générateurs pour le mois j en kWh PCI
     */
    public function cecs_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationEcsEngine $item): float => $carry += $item->cecs_j($mois, $scenario_depensier), 0);
    }

    public function surface_reference(): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationEcsEngine $item): float => $carry += $item->surface_reference(), 0);
    }

    /**
     * Ratio de dimensionnement de l'installation d'ECS
     * 
     * @param Id $id - Identifiant de l'installation d'ECS
     */
    public function rdim(Id $id): ?float
    {
        if (null === $engine = $this->get($id)) {
            return null;
        }
        return $engine->surface_reference() / $this->surface_reference();
    }

    /** @return InstallationEcsEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * @param Id $id - Identifiant de l'installation d'ECS
     */
    public function get(Id $id): ?InstallationEcsEngine
    {
        $collection = \array_filter($this->collection, fn (InstallationEcsEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    public function __invoke(InstallationEcsCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (InstallationEcs $item): InstallationEcsEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
