<?php

namespace App\Domain\Lnc\Engine;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Entity\{Baie, BaieCollection};
use App\Domain\Lnc\LncEngine;

final class BaieEngineCollection
{
    /** @var BaieEngine[] */
    private array $collection = [];

    public function __construct(private BaieEngine $engine)
    {
    }

    /**
     * Somme des surfaces des baies constitutives de la véranda
     */
    public function surface(): float
    {
        return \array_reduce($this->collection, fn (float $carry, BaieEngine $item): float => $carry + $item->surface(), 0);
    }

    /**
     * Surface sud équivalente des apports dans la véranda pour le mois j
     */
    public function sst_j(Mois $mois): float
    {
        return \array_reduce($this->collection, fn (float $carry, BaieEngine $item): float => $carry + $item->sst_j($mois), 0);
    }

    /**
     * t,k - Coefficient de transparence de la véranda
     * 
     * Dans le cas où les vitrages séparant l'espace tampon solarisé de l'extérieur sont hétérogènes, le coefficient T
     * est celui du vitrage majoritaire. Dans le cas où aucun vitrage n'est majoritaire, le coefficient T est proratisé
     * à la surface.
     * 
     * TODO: inclure la cas où un vitrage est majoritaire
     */
    public function t(): float
    {
        if (0 === $surface = $this->surface()) {
            return 0;
        }
        return \array_reduce($this->collection, fn (float $carry, BaieEngine $item): float => $carry + $item->t() * ($item->surface() / $surface), 0);
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

    public function __invoke(BaieCollection $input, LncEngine $engine): self
    {
        $engine = clone $this;
        $engine->collection = \array_map(fn (Baie $item): BaieEngine => ($this->engine)($item, $engine), $input->to_array());
        return $engine;
    }
}
