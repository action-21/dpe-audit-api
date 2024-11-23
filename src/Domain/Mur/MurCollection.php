<?php

namespace App\Domain\Mur;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Entity\ParoiCollection;
use App\Domain\Mur\Service\{MoteurPerformance, MoteurSurfaceDeperditive};
use App\Domain\Simulation\Simulation;

/**
 * @see App\Domain\Paroi\Parois::murs()
 * 
 * @property Mur[] $elements
 */
final class MurCollection extends ArrayCollection implements ParoiCollection
{
    public function controle(): void
    {
        $this->walk(fn(Mur $item) => $item->controle());
    }

    public function reinitialise(): static
    {
        return $this->walk(fn(Mur $item) => $item->reinitialise());
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        return $this->walk(fn(Mur $item) => $item->calcule_surface_deperditive($moteur));
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Mur $item) => $item->calcule_performance($moteur, $simulation));
    }

    public function find(Id $id): ?Mur
    {
        return $this->findFirst(fn(mixed $key, Mur $item): bool => $item->id()->compare($id));
    }

    public function filter_by_paroi_ancienne(bool $paroi_ancienne): self
    {
        return $this->filter(fn(Mur $item): bool => $item->caracteristique()->paroi_ancienne === $paroi_ancienne);
    }

    public function filter_by_local_non_chauffe(Id $id): self
    {
        return $this->filter(fn(Mur $item): bool => $item->local_non_chauffe()?->id()->compare($id) ?? false);
    }

    public function filter_by_inertie(bool $inertie): self
    {
        return $this->filter(fn(Mur $item): bool => $item->est_lourd() === $inertie);
    }

    public function filter_by_isolation(bool $isolation): self
    {
        return $this->filter(fn(Mur $item): bool => $item->est_isole() === $isolation);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Mur $item): float => $carry += $item->caracteristique()->surface);
    }

    public function surface_deperditive(): float
    {
        return $this->reduce(fn(float $carry, Mur $item): float => $carry += $item->surface_deperditive());
    }

    public function u(): float
    {
        return ($sdep = $this->surface_deperditive()) ? $this->dp() / $sdep : 0;
    }

    public function dp(): float
    {
        return $this->reduce(fn(float $carry, Mur $item): float => $carry += $item->performance()?->dp);
    }
}
