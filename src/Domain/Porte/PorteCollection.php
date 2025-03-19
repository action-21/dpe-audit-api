<?php

namespace App\Domain\Porte;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Entity\ParoiCollection;
use App\Domain\Porte\Service\MoteurPerformance;

/**
 * @see App\Domain\Paroi\Parois::portes()
 * 
 * @property Porte[] $elements
 */
final class PorteCollection extends ArrayCollection implements ParoiCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Porte $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Porte $item) => $item->reinitialise());
    }

    public function calcule_performance(MoteurPerformance $moteur): static
    {
        return $this->walk(fn(Porte $item) => $item->calcule_performance($moteur));
    }

    public function find(Id $id): ?Porte
    {
        return $this->findFirst(fn(mixed $key, Porte $item): bool => $item->id->compare($id));
    }

    public function filter_by_paroi(Id $id): static
    {
        return $this->filter(fn(Porte $item): bool => $item->paroi()?->id->compare($id) ?? false);
    }

    public function filter_by_local_non_chauffe(Id $id): static
    {
        return $this->filter(fn(Porte $item): bool => $item->local_non_chauffe()?->id()->compare($id) ?? false);
    }

    public function filter_by_isolation(bool $isolation): static
    {
        return $this->filter(fn(Porte $item): bool => $item->isolation(defaut: true)->est_isole() === $isolation);
    }

    public function filter_by_presence_joint(bool $presence_joint): static
    {
        return $this->filter(fn(Porte $item): bool => $item->menuiserie()->presence_joint === $presence_joint);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Porte $item): float => $carry += $item->position()->surface);
    }

    public function surface_deperditive(): float
    {
        return $this->reduce(fn(float $carry, Porte $item): float => $carry += $item->performance()?->sdep);
    }

    public function u(): float
    {
        return ($sdep = $this->surface_deperditive()) ? $this->dp() / $sdep : 0;
    }

    public function dp(): float
    {
        return $this->reduce(fn(float $carry, Porte $item): float => $carry += $item->performance()?->dp);
    }
}
