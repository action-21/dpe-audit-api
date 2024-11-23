<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Refroidissement\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};
use App\Domain\Refroidissement\ValueObject\Performance;

final class Systeme
{
    private ?float $rdim = null;
    private ?ConsommationCollection $consommations = null;

    public function __construct(
        private Id $id,
        private Installation $installation,
        private Generateur $generateur,
    ) {}

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->consommations = null;
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->rdim = $moteur->calcule_dimensionnement_systeme($this);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->consommations = $moteur->calcule_consommations($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }

    public function consommations(): ?ConsommationCollection
    {
        return $this->consommations;
    }
}
