<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Enum\Mitoyennete;
use App\Domain\Lnc\Service\MoteurSurfaceDeperditive;

/**
 * @property Paroi[] $elements
 */
final class ParoiCollection extends ArrayCollection
{
    public function controle(): void
    {
        $this->walk(fn(Paroi $item) => $item->controle());
    }

    public function reinitialise(): static
    {
        return $this->walk(fn(Paroi $item) => $item->reinitialise());
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        return $this->walk(fn(Paroi $entity) => $entity->calcule_surface_deperditive($moteur));
    }

    public function find(Id $id): ?Paroi
    {
        return $this->findFirst(fn(mixed $key, Paroi $item): bool => $item->id()->compare($id));
    }

    public function filter_by_mitoyennete(Mitoyennete $mitoyennete): self
    {
        return $this->filter(fn(Paroi $item): bool => $item->position()->mitoyennete() === $mitoyennete);
    }

    public function filter_by_isolation(bool $isolation): self
    {
        return $this->filter(fn(Paroi $item): bool => $item->est_isole() === $isolation);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Paroi $item): float => $carry += $item->surface());
    }

    public function aue(?bool $isolation = null): float
    {
        $collection = $isolation === null ? $this : $this->filter_by_isolation($isolation);
        return $collection->reduce(fn(float $carry, Paroi $item): float => $carry += $item->aue());
    }

    public function aiu(?bool $isolation = null): float
    {
        $collection = $isolation === null ? $this : $this->filter_by_isolation($isolation);
        return $collection->reduce(fn(float $carry, Paroi $item): float => $carry += $item->aiu());
    }

    public function isolation_aue(): bool
    {
        return $this->aue(isolation: true) > $this->aue();
    }

    public function isolation_aiu(): bool
    {
        return $this->aiu(isolation: true) > $this->aiu();
    }
}
