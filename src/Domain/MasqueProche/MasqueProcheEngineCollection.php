<?php

namespace App\Domain\MasqueProche;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class MasqueProcheEngineCollection
{
    /** @var MasqueProcheEngine[] */
    private array $collection = [];

    public function __construct(private MasqueProcheEngine $engine)
    {
    }

    /**
     * Facteur de réduction de l'ensoleillement dû aux masques proches
     */
    public function fe1(?Id $baie_id = null): float
    {
        $collection = $baie_id ? $this->search($baie_id) : $this->collection;
        return \min(1, \array_map(fn (MasqueProcheEngine $item): float => $item->fe1(), $collection));
    }

    /** @return MasqueLointainEngine[] */
    private function search(Id $baie_id): array
    {
        return \array_filter($this->collection, fn (MasqueProcheEngine $item): bool => $item->input()->id()->compare($baie_id));
    }

    /**
     * @param Id $id - Identifiant du masque proche
     */
    public function get(Id $id): ?MasqueProcheEngine
    {
        $collection = \array_filter($this->collection, fn (MasqueProcheEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return MasqueLointainEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(MasqueProcheCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (MasqueProche $item): MasqueProcheEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
