<?php

namespace App\Domain\Photovoltaique\Engine;

use App\Domain\Common\Enum\Mois;
use App\Domain\Photovoltaique\Entity\{PanneauPhotovoltaique, PanneauPhotovoltaiqueCollection};
use App\Domain\Photovoltaique\InstallationPhotovoltaiqueEngine;

final class PanneauPhotovoltaiqueEngineCollection
{
    /** @var PanneauPhotovoltaiqueEngine[] */
    private array $collection = [];

    public function __construct(private PanneauPhotovoltaiqueEngine $engine)
    {
    }

    /**
     * Production d'électricité photovoltaïque annuelle en kWh
     */
    public function ppv(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PanneauPhotovoltaiqueEngine $item): float => $carry + $item->ppv(), 0);
    }

    /**
     * Production d'électricité photovoltaïque pour le mois j en kWh
     */
    public function ppv_j(Mois $mois): float
    {
        return \array_reduce($this->collection, fn (float $carry, PanneauPhotovoltaiqueEngine $item): float => $carry + $item->ppv_j($mois), 0);
    }

    public function get(PanneauPhotovoltaique $entity): ?PanneauPhotovoltaiqueEngine
    {
        $collection = \array_filter($this->collection, fn (PanneauPhotovoltaiqueEngine $item): bool => $item->input()->id()->compare($entity->id()));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return PanneauPhotovoltaiqueEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(PanneauPhotovoltaiqueCollection $input, InstallationPhotovoltaiqueEngine $engine): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (PanneauPhotovoltaique $item): PanneauPhotovoltaiqueEngine => ($this->engine)($item, $engine), $input->to_array());
        return $engine;
    }
}
