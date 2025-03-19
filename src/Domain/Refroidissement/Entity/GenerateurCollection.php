<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Service\MoteurPerformance;

/**
 * @property Generateur[] $elements
 */
final class GenerateurCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Generateur $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Generateur $item) => $item->reinitialise());
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        return $this->walk(fn(Generateur $item) => $item->calcule_performance($moteur));
    }

    public function find(Id $id): ?Generateur
    {
        return $this->findFirst(fn(mixed $key, Generateur $item): bool => $item->id()->compare($id));
    }
}
