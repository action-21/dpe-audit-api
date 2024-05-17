<?php

namespace App\Domain\MasqueLointain;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class MasqueLointainEngineCollection
{
    /** @var MasqueLointainEngine[] */
    private array $collection = [];

    public function __construct(private MasqueLointainEngine $engine)
    {
    }

    /**
     * Facteur de réduction de l'ensoleillement dû aux masques lointains
     */
    public function fe2(?Orientation $orientation = null): float
    {
        $collection = $orientation ? $this->search($orientation) : $this->collection;
        $collection = \array_filter($collection, fn (MasqueLointainEngine $item): bool => $item->calcul_fe2());
        $fe2 = \array_reduce($collection, fn (float $carry, MasqueLointainEngine $item): float => $carry = \min($carry, $item->fe2()), 1);
        $omb = \min($this->omb($orientation), 0) / 100;

        return \min($fe2, 1 - $omb);
    }

    /**
     * Somme des ombrages due aux masques lointains
     */
    public function omb(?Orientation $orientation = null): float
    {
        $collection = $orientation ? $this->search($orientation) : $this->collection;
        $collection = \array_filter($collection, fn (MasqueLointainEngine $item): bool => $item->calcul_omb());
        return \array_reduce($collection, fn (float $carry, MasqueLointainEngine $item): float => $carry += $item->omb(), 0);
    }

    /** @return MasqueLointainEngine[] */
    private function search(Orientation $orientation): array
    {
        return \array_filter($this->collection, fn (MasqueLointainEngine $item) => $item->orientation() === $orientation);
    }

    /**
     * @param Id $id - Identifiant du masque lointain
     */
    public function get(Id $id): ?MasqueLointainEngine
    {
        $collection = \array_filter($this->collection, fn (MasqueLointainEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return MasqueLointainEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(MasqueLointainCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (MasqueLointain $item): MasqueLointainEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
