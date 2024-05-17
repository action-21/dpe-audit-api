<?php

namespace App\Domain\PontThermique;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class PontThermiqueEngineCollection
{
    /** @var PontThermiqueEngine[] */
    private array $collection = [];

    public function __construct(private PontThermiqueEngine $engine)
    {
    }

    /**
     * ∑pt,pont_thermique - Somme des ponts thermiques
     */
    public function pt(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PontThermiqueEngine $item): float => $carry += $item->pt(), 0);
    }

    /**
     * ∑l,pont_thermique - Somme des longieurs des ponts thermiques
     */
    public function l(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PontThermiqueEngine $item): float => $carry += $item->l(), 0);
    }

    /**
     * ∑k,pont_thermique - Moyenne pondérée des valeurs ponts thermiques
     */
    public function k(): float
    {
        return \array_reduce($this->collection, fn (float $carry, PontThermiqueEngine $item): float => $carry += $item->k() * ($item->l() / $this->l() ?? 1), 0);
    }

    /**
     * @param Id $id - Identifiant du pont thermique
     */
    public function get(Id $id): ?PontThermiqueEngine
    {
        $collection = \array_filter($this->collection, fn (PontThermiqueEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return PontThermiqueEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(PontThermiqueCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (PontThermique $item): PontThermiqueEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
