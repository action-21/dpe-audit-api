<?php

namespace App\Domain\Climatisation;

use App\Domain\Climatisation\InstallationClimatisationEngine;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class InstallationClimatisationEngineCollection
{
    /** @var InstallationClimatisationEngine[] */
    private array $collection = [];

    public function __construct(private InstallationClimatisationEngine $engine)
    {
    }

    /**
     * ∑cfr - Somme des consommations de refroidissement en kWh
     */
    public function cfr(bool $scenario_depensier = false, bool $energie_primaire = false): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationClimatisationEngine $item): float => $carry += $item->cfr($scenario_depensier, $energie_primaire), 0);
    }

    /**
     * ∑cfr,j - Somme des consommations de refroidissement pour le mois j en kWh
     */
    public function cfr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationClimatisationEngine $item): float => $carry += $item->cfr_j($mois, $scenario_depensier), 0);
    }

    /**
     * Coefficient d'efficience énergétique pondéré par la surface climatisée
     */
    public function eer(): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationClimatisationEngine $item): float => $carry += $item->eer() * $item->rdim(), 0);
    }

    /**
     * @param Id $id - Identifiant de l'installation de climatisation
     */
    public function get(Id $id): ?InstallationClimatisationEngine
    {
        $collection = \array_filter($this->collection, fn (InstallationClimatisationEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return InstallationClimatisationEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(InstallationClimatisationCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (InstallationClimatisation $item): InstallationClimatisationEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
