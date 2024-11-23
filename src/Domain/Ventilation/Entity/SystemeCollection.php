<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ventilation\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};

/**
 * @property Systeme[] $elements
 */
final class SystemeCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function controle(): void
    {
        $this->walk(fn(Systeme $item) => $item->controle());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_dimensionnement($moteur));
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_performance($moteur));
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_consommations($moteur));
    }

    public function find(Id $id): ?Systeme
    {
        return $this->findFirst(fn(mixed $key, Systeme $item): bool => $item->id()->compare($id));
    }

    public function qvarep_conv(): float
    {
        return $this->reduce(fn(float $carry, Systeme $item): float => $carry += $item->performance()?->qvarep_conv * $item->rdim());
    }

    public function qvasouf_conv(): float
    {
        return $this->reduce(fn(float $carry, Systeme $item): float => $carry += $item->performance()?->qvasouf_conv * $item->rdim());
    }

    public function smea_conv(): float
    {
        return $this->reduce(fn(float $carry, Systeme $item): float => $carry += $item->performance()?->smea_conv * $item->rdim());
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Systeme $item): ConsommationCollection => $collection->merge(
            $item->consommations(),
        ), new ConsommationCollection());
    }
}
