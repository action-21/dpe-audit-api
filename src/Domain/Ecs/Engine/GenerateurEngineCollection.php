<?php

namespace App\Domain\Ecs\Engine;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Entity\{Generateur, GenerateurCollection};
use App\Domain\Ecs\InstallationEcsEngine;

final class GenerateurEngineCollection
{
    /** @var GenerateurEngine[] */
    private array $collection;

    public function __construct(private GenerateurEngine $engine)
    {
    }

    /**
     * Consommation annuelle des générateurs en kWh PCI
     */
    public function cecs(bool $scenario_depensier = false): float
    {
        return \array_reduce($this->collection, fn (float $carry, GenerateurEngine $item): float => $carry += $item->cecs($scenario_depensier), 0);
    }

    /**
     * Consommation des générateurs pour le mois j en kWh PCI
     */
    public function cecs_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return \array_reduce($this->collection, fn (float $carry, GenerateurEngine $item): float => $carry += $item->cecs_j($mois, $scenario_depensier), 0);
    }

    /**
     * @param Id $id - Identifiant du générateur
     */
    public function get(Id $id): ?GenerateurEngine
    {
        $collection = \array_filter($this->collection, fn (GenerateurEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return GenerateurEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(GenerateurCollection $input, InstallationEcsEngine $engine): self
    {
        $service = clone $this;
        $service->collection = \array_map(fn (Generateur $item): GenerateurEngine => ($this->engine)($item, $engine), $input->to_array());
        return $service;
    }
}
