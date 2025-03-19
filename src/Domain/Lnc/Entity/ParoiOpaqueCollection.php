<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Enum\Mitoyennete;
use App\Domain\Lnc\Service\MoteurSurfaceDeperditiveParoiOpaque;

/**
 * @property ParoiOpaque[] $elements
 */
final class ParoiOpaqueCollection extends ParoiCollection
{
    public function controle(): void
    {
        $this->walk(fn(ParoiOpaque $item) => $item->controle());
    }

    public function reinitialise(): static
    {
        return $this->walk(fn(ParoiOpaque $item) => $item->reinitialise());
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditiveParoiOpaque $moteur): self
    {
        return $this->walk(fn(ParoiOpaque $entity) => $entity->calcule_surface_deperditive($moteur));
    }

    public function find(Id $id): ?ParoiOpaque
    {
        return $this->findFirst(fn(mixed $key, ParoiOpaque $item): bool => $item->id()->compare($id));
    }

    public function filter_by_mitoyennete(Mitoyennete $mitoyennete): self
    {
        return $this->filter(fn(ParoiOpaque $item): bool => $item->position()->mitoyennete === $mitoyennete);
    }

    public function filter_by_isolation(bool $isolation): self
    {
        return $this->filter(fn(ParoiOpaque $item): bool => $item->surface_deperditive()?->isolation->boolval() === $isolation);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, ParoiOpaque $item): float => $carry += $item->position()->surface);
    }

    public function aue(?bool $isolation = null): float
    {
        $collection = $isolation === null ? $this : $this->filter_by_isolation($isolation);
        return $collection->reduce(fn(float $carry, ParoiOpaque $item): float => $carry += $item->surface_deperditive()?->aue);
    }

    public function aiu(?bool $isolation = null): float
    {
        $collection = $isolation === null ? $this : $this->filter_by_isolation($isolation);
        return $collection->reduce(fn(float $carry, ParoiOpaque $item): float => $carry += $item->surface_deperditive()?->aiu);
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
