<?php

namespace App\Domain\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\{BesoinCollection, ConsommationCollection};
use App\Domain\Refroidissement\Entity\{Generateur, GenerateurCollection, Installation, InstallationCollection};
use App\Domain\Refroidissement\Service\{MoteurBesoin, MoteurConsommation, MoteurDimensionnement, MoteurPerformance};
use App\Domain\Simulation\Simulation;

final class Refroidissement
{
    private ?BesoinCollection $besoins = null;

    public function __construct(
        private readonly Audit $audit,
        private GenerateurCollection $generateurs,
        private InstallationCollection $installations,
    ) {}

    public function controle(): void
    {
        $this->generateurs->controle();
        $this->installations->controle();
    }

    public function reinitialise(): void
    {
        $this->besoins = null;
        $this->generateurs->reinitialise();
        $this->installations->reinitialise();
    }

    public function calcule_besoins(MoteurBesoin $moteur, Simulation $simulation): self
    {
        $this->besoins = $moteur->calcule_besoins($this, $simulation);
        return $this;
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->installations->calcule_dimensionnement($moteur);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->generateurs->calcule_performance($moteur);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->installations->calcule_consommations($moteur);
        return $this;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function installations(): InstallationCollection
    {
        return $this->installations;
    }

    public function add_installation(Installation $entity): self
    {
        $this->installations->add($entity);
        return $this;
    }

    public function remove_installation(Installation $entity): self
    {
        $this->installations->remove($entity);
        return $this;
    }

    public function generateurs(): GenerateurCollection
    {
        return $this->generateurs;
    }

    public function add_generateur(Generateur $entity): self
    {
        $this->generateurs->add($entity);
        return $this;
    }

    public function remove_generateur(Generateur $entity): self
    {
        $this->generateurs->remove($entity);
        return $this;
    }

    public function annee_construction_batiment(): int
    {
        return $this->audit->annee_construction_batiment();
    }

    public function besoins(): ?BesoinCollection
    {
        return $this->besoins;
    }

    public function consommations(): ConsommationCollection
    {
        return $this->installations->consommations();
    }
}
