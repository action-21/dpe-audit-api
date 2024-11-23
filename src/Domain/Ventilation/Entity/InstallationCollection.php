<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ventilation\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};

/**
 * @property Installation[] $elements
 */
final class InstallationCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Installation $item) => $item->reinitialise());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_dimensionnement($moteur));
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_performance($moteur));
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        return $this->walk(fn(Installation $item) => $item->calcule_consommations($moteur));
    }

    public function controle(): void
    {
        $this->walk(fn(Installation $item) => $item->controle());
    }

    public function find(Id $id): ?Installation
    {
        return $this->findFirst(fn(mixed $key, Installation $item): bool => $item->id()->compare($id));
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Installation $item): float => $carry += $item->surface());
    }

    public function qvarep_conv(): float
    {
        return $this->reduce(fn(float $carry, Installation $item): float => $carry += $item->systemes()->qvarep_conv() * $item->rdim());
    }

    public function qvasouf_conv(): float
    {
        return $this->reduce(fn(float $carry, Installation $item): float => $carry += $item->systemes()->qvasouf_conv() * $item->rdim());
    }

    public function smea_conv(): float
    {
        return $this->reduce(fn(float $carry, Installation $item): float => $carry += $item->systemes()->smea_conv() * $item->rdim());
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Installation $item): ConsommationCollection => $collection->merge(
            $item->systemes()->consommations(),
        ), new ConsommationCollection());
    }
}
