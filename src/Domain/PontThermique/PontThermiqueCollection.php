<?php

namespace App\Domain\PontThermique;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\Service\MoteurPerformance;

/**
 * @see App\Domain\Enveloppe\Enveloppe::ponts_thermiques()
 * 
 * @property PontThermique[] $elements
 */
final class PontThermiqueCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(PontThermique $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(PontThermique $item) => $item->reinitialise());
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        return $this->walk(fn(PontThermique $item) => $item->calcule_performance($moteur));
    }

    public function find(Id $id): ?PontThermique
    {
        return $this->findFirst(fn(mixed $key, PontThermique $item): bool => $item->id()->compare($id));
    }

    public function pt(): float
    {
        return $this->reduce(fn(float $carry, PontThermique $item): float => $carry += $item->performance()?->pt);
    }
}
