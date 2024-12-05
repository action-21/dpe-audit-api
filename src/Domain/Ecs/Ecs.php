<?php

namespace App\Domain\Ecs;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\{BesoinCollection, ConsommationCollection};
use App\Domain\Ecs\Entity\{Generateur, GenerateurCollection, Installation, InstallationCollection};
use App\Domain\Ecs\Service\{MoteurBesoin, MoteurConsommation, MoteurDimensionnement, MoteurPerformance, MoteurPerte, MoteurRendement};
use App\Domain\Simulation\Simulation;

final class Ecs
{
    private ?BesoinCollection $besoins = null;

    public function __construct(
        private readonly Audit $audit,
        private InstallationCollection $installations,
        private GenerateurCollection $generateurs,
    ) {}

    public function reinitialise(): void
    {
        $this->besoins = null;
        $this->generateurs->reinitialise();
        $this->installations->reinitialise();
    }

    public function controle(): void
    {
        $this->generateurs->controle();
        $this->installations->controle();
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

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        $this->generateurs->calcule_performance($moteur, $simulation);
        return $this;
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        $this->generateurs->calcule_pertes($moteur, $simulation);
        $this->installations->calcule_pertes($moteur, $simulation);
        return $this;
    }

    public function calcule_rendement(MoteurRendement $moteur): self
    {
        $this->installations->calcule_rendement($moteur);
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

    public function besoins(): ?BesoinCollection
    {
        return $this->besoins;
    }

    public function consommations(): ConsommationCollection
    {
        return $this->installations->consommations();
    }

    // * helpers

    public function annee_construction_batiment(): int
    {
        return $this->audit->annee_construction_batiment();
    }
}
