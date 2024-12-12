<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Enum\{TypeChauffage};
use App\Domain\Chauffage\Service\{MoteurConsommation, MoteurDimensionnement, MoteurRendement};
use App\Domain\Chauffage\ValueObject\{RendementCollection, Reseau};
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Simulation\Simulation;
use Webmozart\Assert\Assert;

final class Systeme
{
    private ?float $rdim = null;
    private ?RendementCollection $rendements = null;
    private ?ConsommationCollection $consommations = null;
    private ?ConsommationCollection $consommations_auxiliaires = null;

    public function __construct(
        private readonly Id $id,
        private readonly Installation $installation,
        private Generateur $generateur,
        private ?Reseau $reseau,
        private EmetteurCollection $emetteurs,
    ) {}

    public static function create(
        Id $id,
        Installation $installation,
        Generateur $generateur,
        ?Reseau $reseau,
    ): self {
        if (false === $generateur->type()->is_chauffage_divise()) {
            Assert::notNull($reseau);
        }
        return new self(
            id: $id,
            installation: $installation,
            generateur: $generateur,
            reseau: $generateur->type()->is_chauffage_central() ? $reseau : null,
            emetteurs: new EmetteurCollection(),
        );
    }

    public function controle(): void
    {
        if (false === $this->generateur->type()->is_chauffage_central()) {
            Assert::null($this->reseau);
        }
        if (false === $this->generateur->type()->is_chauffage_divise()) {
            Assert::notNull($this->reseau);
        }
    }

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->rendements = null;
        $this->consommations = null;
        $this->consommations_auxiliaires = null;
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->rdim = $moteur->calcule_dimensionnement_systeme($this);
        return $this;
    }

    public function calcule_rendement(MoteurRendement $moteur, Simulation $simulation): self
    {
        $this->rendements = $moteur->calcule_rendement($this, $simulation);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur, Simulation $simulation): self
    {
        $this->consommations = $moteur->calcule_consommations($this, $simulation);
        $this->consommations_auxiliaires = $moteur->calcule_consommations_auxiliaires($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function chauffage(): Chauffage
    {
        return $this->installation->chauffage();
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function effet_joule(): bool
    {
        return $this->generateur->effet_joule();
    }

    public function type_chauffage(): TypeChauffage
    {
        return $this->reseau ? TypeChauffage::CHAUFFAGE_CENTRAL : TypeChauffage::CHAUFFAGE_DIVISE;
    }

    public function reseau(): ?Reseau
    {
        return $this->reseau;
    }

    public function emetteurs(): EmetteurCollection
    {
        return $this->emetteurs;
    }

    public function reference_emetteur(Emetteur $entity): self
    {
        if ($this->type_chauffage() === TypeChauffage::CHAUFFAGE_CENTRAL) {
            $this->emetteurs->add($entity);
        }
        return $this;
    }

    public function dereference_emetteur(Emetteur $entity): self
    {
        $this->emetteurs->remove($entity);
        return $this;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }

    public function pn(): ?float
    {
        return $this->generateur->signaletique()?->pn ?? $this->generateur->performance()?->pn;
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
