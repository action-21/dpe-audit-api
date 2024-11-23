<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Refroidissement\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};

/**
 * @property Systeme[] $elements
 */
final class SystemeCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_dimensionnement($moteur));
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_consommations($moteur));
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Systeme $item): ConsommationCollection => $collection->merge(
            $item->consommations(),
        ), new ConsommationCollection());
    }
}
