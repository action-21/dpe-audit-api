<?php

namespace App\Domain\Logement;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class LogementEngineCollection
{
    /** @var LogementEngine[] */
    private array $collection = [];

    public function __construct(private LogementEngine $engine)
    {
    }

    /**
     * @param Id $id - Identifiant du logement
     */
    public function get(Id $id): ?LogementEngine
    {
        $collection = \array_filter($this->collection, fn (LogementEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return LogementEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(LogementCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (Logement $item): LogementEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
