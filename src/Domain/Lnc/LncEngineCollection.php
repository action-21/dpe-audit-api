<?php

namespace App\Domain\Lnc;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\SimulationEngine;

final class LncEngineCollection
{
    /** @var LncEngine[] */
    private array $collection = [];

    public function __construct(private LncEngine $engine)
    {
    }

    /**
     * Coefficient de réduction des déperditions thermiques des parois donnant sur un local non chauffé
     */
    public function b(Id $id): ?float
    {
        return $this->get($id)?->b();
    }

    /**
     * Surface sud équivalente des apports dans la véranda par la baie k pour le mois j
     */
    public function sst_j(Id $id, Mois $mois): ?float
    {
        return $this->get($id)?->sst_j($mois);
    }

    /**
     * t,k - Coefficient de transparence de la véranda
     */
    public function t(Id $id): ?float
    {
        return $this->get($id)?->t();
    }

    /**
     * @param Id $id - Identifiant du local non chauffé
     */
    public function get(Id $id): ?LncEngine
    {
        $collection = \array_filter($this->collection, fn (LncEngine $item): bool => $item->input()->id()->compare($id));
        return \count($collection) > 0 ? \current($collection) : null;
    }

    /** @return LncEngine[] */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function __invoke(LncCollection $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (Lnc $item): LncEngine => ($this->engine)($item, $context), $input->to_array());
        return $engine;
    }
}
