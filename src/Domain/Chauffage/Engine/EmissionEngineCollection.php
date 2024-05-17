<?php

namespace App\Domain\Chauffage\Engine;

use App\Domain\Chauffage\Entity\{Emission, EmissionCollection};
use App\Domain\Common\ValueObject\Id;

final class EmissionEngineCollection
{
    /** @var EmissionEngine[] */
    private array $collection = [];

    public function __construct(private EmissionEngine $engine)
    {
    }

    /**
     * INT - Facteur d'intermittence moyen pondéré
     */
    public function int(): float
    {
        return \array_reduce($this->collection, fn (float $carry, EmissionEngine $item): float => $carry += $item->int() * $item->rdim(), 0);
    }

    /**
     * ich,e - Inverse du rendement de chauffage de l'emission
     */
    public function ich_emission(): float
    {
        return \array_reduce($this->collection, fn (float $carry, EmissionEngine $item): float => $carry += $item->ich_emission() * $item->rdim(), 0);
    }

    /**
     * @param Id $id - Identifiant de la ventilation
     */
    public function get(Id $id): ?EmissionEngine
    {
        $collection = \array_filter($this->collection, fn (EmissionEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return EmissionEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(EmissionCollection $input, GenerateurEngine $engine): self
    {
        $service = clone $this;
        $service->collection = \array_map(fn (Emission $item): EmissionEngine => ($this->engine)($item, $engine), $input->to_array());
        return $service;
    }
}
