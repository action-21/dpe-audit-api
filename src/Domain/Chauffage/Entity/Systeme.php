<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Enum\{CategorieGenerateur, TypeDistribution};
use App\Domain\Chauffage\Service\{MoteurConsommation, MoteurDimensionnement, MoteurRendement};
use App\Domain\Chauffage\ValueObject\{Performance, PerteCollection, RendementCollection, Reseau};
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Simulation\Simulation;

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
        private TypeDistribution $type_distribution,
        private ?Reseau $reseau,
        private bool $position_volume_chauffe,
        private EmetteurCollection $emetteurs,
    ) {}

    public function set_systeme_divise(Generateur $generateur): self
    {
        if (false === $generateur->type()->chauffage_divise())
            throw new \InvalidArgumentException('Générateur incompatible');

        $this->generateur = $generateur;
        $this->reseau = null;
        $this->position_volume_chauffe = true;
        $this->type_distribution = TypeDistribution::SANS;
        $this->emetteurs = new EmetteurCollection();
        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function set_systeme_central(
        Generateur $generateur,
        TypeDistribution $type_distribution,
        Reseau $reseau,
        bool $position_volume_chauffe,
    ): self {
        if (false === $generateur->type()->chauffage_central())
            throw new \InvalidArgumentException('Générateur incompatible');

        if (\in_array($generateur->categorie(), [
            CategorieGenerateur::CHAUDIERE_MULTI_BATIMENT,
            CategorieGenerateur::PAC_MULTI_BATIMENT,
            CategorieGenerateur::RESEAU_CHALEUR,
        ])) $position_volume_chauffe = false;

        $this->generateur = $generateur;
        $this->type_distribution = $type_distribution;
        $this->reseau = $reseau;
        $this->position_volume_chauffe = $position_volume_chauffe;
        $this->emetteurs = new EmetteurCollection();
        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function controle(): void
    {
        $this->reseau?->controle();

        if (false === \in_array($this->type_distribution, $this->categorie()->types_distribution(type_generateur: $this->generateur->type()))) {
            dd($this->type_distribution, $this->generateur->type(), $this->generateur->categorie());
            throw new \InvalidArgumentException('Type de distribution incompatible');
        }

        if (false === $this->generateur->type()->chauffage_central())
            Assert::egal($this->emetteurs->count(), 0);
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

    public function categorie(): CategorieGenerateur
    {
        return $this->generateur->categorie();
    }

    public function effet_joule(): bool
    {
        return $this->generateur->effet_joule();
    }

    public function is_systeme_central(): bool
    {
        return $this->reseau !== null;
    }

    public function is_systeme_divise(): bool
    {
        return $this->reseau === null;
    }

    public function type_distribution(): TypeDistribution
    {
        return $this->type_distribution;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
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
        if (false === $this->generateur->type()->chauffage_central())
            throw new \InvalidArgumentException('Impossible d\'ajouter un émetteur à un système de chauffage divisé.');

        $this->emetteurs->add($entity);
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
