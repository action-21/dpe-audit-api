<?php

namespace App\Domain\Baie;

use App\Domain\Baie\Enum\QualiteComposant;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class BaieEngineCollection
{
    /** @var BaieEngine[] */
    private array $collection = [];

    public function __construct(private BaieEngine $engine)
    {
    }

    /**
     * ∑dp,baie - Somme des déperditions (m²)
     */
    public function dp(): float
    {
        return \array_reduce($this->collection, fn (float $carry, BaieEngine $item): float => $carry += $item->dp(), 0);
    }

    /**
     * ∑sdep,baie - Somme des surface déperditives (m²)
     */
    public function sdep(): float
    {
        return \array_reduce($this->collection, fn (float $carry, BaieEngine $item): float => $carry += $item->sdep(), 0);
    }

    /**
     * μ∑u,baie - Coefficient de transmission thermique moyen (W/(m².K))
     */
    public function u(): float
    {
        return ($sdep = $this->sdep())
            ? \array_reduce($this->collection, fn (float $carry, BaieEngine $item): float => $carry += $item->u() * ($item->sdep() / $sdep), 0)
            : 0;
    }

    /**
     * Somme des surfaces sud équivalentes pour le mois j (m²)
     */
    public function sse_j(Mois $mois): float
    {
        return \array_reduce($this->collection, fn (float $carry, BaieEngine $item): float => $carry += $item->sse_j($mois), 0);
    }

    /**
     * Indicateur de performance moyen des planchers hauts
     */
    public function qualite_composant(): QualiteComposant|false
    {
        return ($u = $this->u()) ? QualiteComposant::from_ubaie($u) : false;
    }

    /**
     * @param Id $id - Identifiant de la baie
     */
    public function get(Id $id): ?BaieEngine
    {
        $collection = \array_filter($this->collection, fn (BaieEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return BaieEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(BaieCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (Baie $item): BaieEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
