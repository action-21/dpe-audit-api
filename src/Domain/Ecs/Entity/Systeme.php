<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerte, MoteurRendement};
use App\Domain\Ecs\ValueObject\{PerteCollection, RendementCollection, Reseau, Stockage};
use App\Domain\Simulation\Simulation;

final class Systeme
{
    private ?float $rdim = null;
    private ?PerteCollection $pertes_distribution = null;
    private ?PerteCollection $pertes_stockage = null;
    private ?RendementCollection $rendements = null;
    private ?ConsommationCollection $consommations = null;
    private ?ConsommationCollection $consommations_auxiliaires = null;

    public function __construct(
        private readonly Id $id,
        private readonly Installation $installation,
        private Generateur $generateur,
        private Reseau $reseau,
        private ?Stockage $stockage,
    ) {}

    public function update(Reseau $reseau, ?Stockage $stockage,): self
    {
        $this->reseau = $reseau;
        $this->stockage = $stockage;
        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->pertes_distribution = null;
        $this->pertes_stockage = null;
        $this->rendements = null;
        $this->consommations = null;
    }

    public function controle(): void
    {
        $this->reseau->controle();
        $this->stockage?->controle();
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->rdim = $moteur->calcule_dimensionnement_systeme($this);
        return $this;
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        $this->pertes_distribution = $moteur->calcule_pertes_distribution($this, $simulation);
        $this->pertes_stockage = $moteur->calcule_pertes_stockage_systeme($this, $simulation);
        return $this;
    }

    public function calcule_rendement(MoteurRendement $moteur): self
    {
        $this->rendements = $moteur->calcule_rendement($this);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->consommations = $moteur->calcule_consommations($this);
        $this->consommations_auxiliaires = $moteur->calcule_consommations_auxiliaires($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ecs(): Ecs
    {
        return $this->installation->ecs();
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function reference_generateur(Generateur $entity): self
    {
        $this->generateur = $entity;
        return $this;
    }

    public function reseau(): Reseau
    {
        return $this->reseau;
    }

    public function stockage(): ?Stockage
    {
        return $this->stockage;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }

    public function pertes_distribution(): ?PerteCollection
    {
        return $this->pertes_distribution;
    }

    public function pertes_stockage(): ?PerteCollection
    {
        return $this->pertes_stockage;
    }

    public function rendements(): ?RendementCollection
    {
        return $this->rendements;
    }

    public function consommations(): ?ConsommationCollection
    {
        return $this->consommations;
    }

    public function consommations_auxiliaires(): ?ConsommationCollection
    {
        return $this->consommations_auxiliaires;
    }
}
