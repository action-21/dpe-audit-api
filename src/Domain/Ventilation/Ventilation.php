<?php

namespace App\Domain\Ventilation;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ventilation\Entity\{Generateur, GenerateurCollection, Installation, InstallationCollection};
use App\Domain\Ventilation\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};

final class Ventilation
{
    public function __construct(
        private readonly Audit $audit,
        private GenerateurCollection $generateurs,
        private InstallationCollection $installations,
    ) {}

    public function reinitialise(): void
    {
        $this->installations->reinitialise();
    }

    public function controle(): void
    {
        $this->generateurs->controle();
        $this->installations->controle();
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->installations->calcule_dimensionnement($moteur);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->installations->calcule_performance($moteur);
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

    public function generateurs(): GenerateurCollection
    {
        return $this->generateurs;
    }

    public function add_generateur(Generateur $entity): self
    {
        $this->generateurs->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function remove_generateur(Generateur $entity): self
    {
        $this->generateurs->remove($entity);
        $this->reinitialise();
        return $this;
    }

    public function installations(): InstallationCollection
    {
        return $this->installations;
    }

    public function add_installation(Installation $entity): self
    {
        $this->installations->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function remove_installation(Installation $entity): self
    {
        $this->installations->remove($entity);
        $this->reinitialise();
        return $this;
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
