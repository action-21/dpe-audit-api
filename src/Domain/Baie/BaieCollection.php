<?php

namespace App\Domain\Baie;

use App\Domain\Baie\Service\{MoteurEnsoleillement, MoteurPerformance};
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Entity\ParoiCollection;

/**
 * @see App\Domain\Paroi\Parois::baies()
 * 
 * @property Baie[] $elements
 */
final class BaieCollection extends ArrayCollection implements ParoiCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Baie $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Baie $item) => $item->reinitialise());
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        return $this->walk(fn(Baie $item) => $item->calcule_performance($moteur));
    }

    public function calcule_ensoleillement(MoteurEnsoleillement $moteur): self
    {
        return $this->walk(fn(Baie $item) => $item->calcule_ensoleillement($moteur));
    }

    public function find(Id $id): ?Baie
    {
        return $this->findFirst(fn(mixed $key, Baie $item): bool => $item->id()->compare($id));
    }

    public function filter_by_paroi(Id $id): static
    {
        return $this->filter(fn(Baie $item): bool => $item->paroi()?->id()->compare($id) ?? false);
    }

    public function filter_by_local_non_chauffe(Id $id): self
    {
        return $this->filter(fn(Baie $item): bool => $item->local_non_chauffe()?->id()->compare($id) ?? false);
    }

    public function filter_by_isolation(bool $isolation): self
    {
        return $this->filter(fn(Baie $item): bool => $item->est_isole() === $isolation);
    }

    public function filter_by_presence_joint(bool $presence_joint): self
    {
        return $this->filter(fn(Baie $item): bool => $item->presence_joint_menuiserie() === $presence_joint);
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Baie $item): float => $carry += $item->caracteristique()->surface);
    }

    public function surface_deperditive(): float
    {
        return $this->reduce(fn(float $carry, Baie $item): float => $carry += $item->surface_deperditive());
    }

    public function u(): float
    {
        $sdep = $this->surface_deperditive();
        return $this->reduce(fn(float $carry, Baie $item): float => $carry += $item->performance()?->u * ($item->surface_deperditive() / $sdep ?? 1));
    }

    public function dp(): float
    {
        return $this->reduce(fn(float $carry, Baie $item): float => $carry += $item->performance()?->dp);
    }

    public function sse(Mois $mois): float
    {
        return $this->reduce(fn(float $sse, Baie $item): float => $sse += $item->ensoleillement()?->find(mois: $mois)->sse);
    }
}
