<?php

namespace App\Domain\Lnc;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Lnc\Service\{MoteurEnsoleillement, MoteurPerformance, MoteurSurfaceDeperditive};

/**
 * @see App\Domain\Enveloppe\Enveloppe::locaux_non_chauffes()
 * 
 * @property Lnc[] $elements
 */
final class LncCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Lnc $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Lnc $item) => $item->reinitialise());
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        return $this->walk(fn(Lnc $item): Lnc => $item->calcule_surface_deperditive($moteur));
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        return $this->walk(fn(Lnc $item): Lnc => $item->calcule_performance($moteur));
    }

    public function calcule_ensoleillement(MoteurEnsoleillement $moteur): self
    {
        return $this->walk(fn(Lnc $item): Lnc => $item->calcule_ensoleillement($moteur));
    }

    public function find(Id $id): ?Lnc
    {
        return $this->findFirst(fn(mixed $key, Lnc $item): bool => $item->id()->compare($id));
    }

    public function search_by_type(TypeLnc $type_lnc): self
    {
        return $this->filter(fn(Lnc $item): bool => $item->type() === $type_lnc);
    }
}
