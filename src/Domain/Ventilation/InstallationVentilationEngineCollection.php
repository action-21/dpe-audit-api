<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;
use App\Domain\Ventilation\InstallationVentilationEngine;

final class InstallationVentilationEngineCollection
{
    /** @var InstallationVentilationEngine[] */
    private array $collection = [];

    public function __construct(private InstallationVentilationEngine $engine)
    {
    }

    /**
     * Consommation annuelle des auxiliaires de ventilation des installations de ventilation en kWhef/an
     */
    public function caux(): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationVentilationEngine $item): float => $carry += $item->caux(), 0);
    }

    /**
     * Consommation annuelle des auxiliaires de ventilation des installations de ventilation pour le mois j en kWhef/an
     */
    public function caux_j(Mois $mois): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationVentilationEngine $item): float => $carry += $item->caux_j($mois), 0);
    }

    /**
     * Moyenne pondérée de qvarep_conv des installations de ventilation
     */
    public function qvarep_conv(): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationVentilationEngine $item): float => $carry += $item->qvarep_conv() * $this->rdim($item->input()->id()), 0);
    }

    /**
     * Moyenne pondérée de qvasouf_conv des installations de ventilation
     */
    public function qvasouf_conv(): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationVentilationEngine $item): float => $carry += $item->qvasouf_conv() * $this->rdim($item->input()->id()), 0);
    }

    /**
     * Moyenne pondérée de smea_conv des installations de ventilation
     */
    public function smea_conv(): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationVentilationEngine $item): float => $carry += $item->smea_conv() * $this->rdim($item->input()->id()), 0);
    }

    /**
     * Somme des surfaces de référence des installations de ventilation en m²
     */
    public function surface_reference(): float
    {
        return \array_reduce($this->collection, fn (float $carry, InstallationVentilationEngine $item): float => $carry += $item->surface_reference(), 0);
    }

    /**
     * Ratio de dimensionnement de l'installation de ventilation correspondant à l'id en paramètre
     * 
     * @param Id $id - Identifiant de l'installation de ventilation
     */
    public function rdim(Id $id): ?float
    {
        return null !== ($surface_reference = $this->get($id)?->surface_reference())
            ? $surface_reference / $this->surface_reference()
            : null;
    }

    /**
     * @param Id $id - Identifiant de l'installation de ventilation
     */
    public function get(Id $id): ?InstallationVentilationEngine
    {
        $collection = \array_filter($this->collection, fn (InstallationVentilationEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return InstallationVentilationEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(InstallationVentilationCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (InstallationVentilation $item): InstallationVentilationEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
