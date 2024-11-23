<?php

namespace App\Domain\Chauffage;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\{Emetteur, EmetteurCollection, Generateur, GenerateurCollection, Installation, InstallationCollection};
use App\Domain\Chauffage\Service\{MoteurBesoin, MoteurConsommation, MoteurDimensionnement, MoteurPerformance, MoteurPerte, MoteurRendement};
use App\Domain\Common\ValueObject\{BesoinCollection, ConsommationCollection};
use App\Domain\Simulation\Simulation;

/**
 * @see App\Domain\Audit\Audit::chauffage()
 */
final class Chauffage
{
    private ?BesoinCollection $besoins = null;
    private ?BesoinCollection $besoins_bruts = null;

    public function __construct(
        private readonly Audit $audit,
        private GenerateurCollection $generateurs,
        private EmetteurCollection $emetteurs,
        private InstallationCollection $installations,
    ) {}

    public function controle(): void
    {
        $this->installations->controle();
        $this->emetteurs->controle();
        $this->generateurs->controle();
    }

    public function reinitialise(): void
    {
        $this->besoins = null;
        $this->generateurs->reinitialise();
        $this->installations->reinitialise();
        $this->emetteurs->reinitialise();
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur, Simulation $simulation): self
    {
        $this->generateurs->calcule_dimensionnement($moteur, $simulation);
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
        return $this;
    }

    public function calcule_besoins(MoteurBesoin $moteur, Simulation $simulation): self
    {
        $this->besoins_bruts = $moteur->calcule_besoins_bruts($this, $simulation);
        $this->besoins = $moteur->calcule_besoins($this, $simulation);
        return $this;
    }

    public function calcule_rendement(MoteurRendement $moteur, Simulation $simulation): self
    {
        $this->installations->calcule_rendement($moteur, $simulation);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur, Simulation $simulation): self
    {
        $this->installations->calcule_consommations($moteur, $simulation);
        return $this;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function effet_joule(): bool
    {
        return $this->installations->effet_joule();
    }

    public function generateurs(): GenerateurCollection
    {
        return $this->generateurs;
    }

    public function add_generateur(Generateur $generateur): self
    {
        $this->generateurs->add($generateur);
        return $this;
    }

    public function remove_generateur(Generateur $generateur): self
    {
        $this->generateurs->remove($generateur);
        return $this;
    }

    public function installations(): InstallationCollection
    {
        return $this->installations;
    }

    public function add_installation(Installation $installation): self
    {
        $this->installations->add($installation);
        return $this;
    }

    public function remove_installation(Installation $installation): self
    {
        $this->installations->remove($installation);
        return $this;
    }

    public function emetteurs(): EmetteurCollection
    {
        return $this->emetteurs;
    }

    public function add_emetteur(Emetteur $emetteur): self
    {
        $this->emetteurs->add($emetteur);
        return $this;
    }

    public function remove_emetteur(Emetteur $emetteur): self
    {
        $this->emetteurs->remove($emetteur);
        return $this;
    }

    public function besoins(): ?BesoinCollection
    {
        return $this->besoins;
    }

    public function besoins_bruts(): ?BesoinCollection
    {
        return $this->besoins_bruts;
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
