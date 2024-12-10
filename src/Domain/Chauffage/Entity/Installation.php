<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Enum\TypeChauffage;
use App\Domain\Chauffage\Service\{MoteurConsommation, MoteurDimensionnement, MoteurRendement};
use App\Domain\Chauffage\ValueObject\{Regulation, Solaire};
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Simulation\Simulation;

/**
 * TODO: Associer l'installation à un logement visité
 * 
 * On limite le nombre de systèmes centraux à 2 par installation
 */
final class Installation
{
    private ?float $rdim = null;
    private ?ConsommationCollection $consommations = null;

    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private string $description,
        private float $surface,
        private bool $comptage_individuel,
        private ?Solaire $solaire,
        private Regulation $regulation_centrale,
        private Regulation $regulation_terminale,
        private SystemeCollection $systemes,
    ) {}

    public function update(
        string $description,
        float $surface,
        bool $comptage_individuel,
        ?Solaire $solaire,
        Regulation $regulation_centrale,
        Regulation $regulation_terminale,
    ): self {
        $this->description = $description;
        $this->surface = $surface;
        $this->comptage_individuel = $comptage_individuel;
        $this->solaire = $solaire;
        $this->regulation_centrale = $regulation_centrale;
        $this->regulation_terminale = $regulation_terminale;

        $this->controle();
        return $this;
    }

    public function controle(): void
    {
        Assert::positif($this->surface);
        $this->solaire?->controle();
        $this->systemes->controle();
    }

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->consommations = null;
        $this->systemes->reinitialise();
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->systemes->calcule_dimensionnement($moteur);
        $this->rdim = $moteur->calcule_dimensionnement_installation($this);
        return $this;
    }

    public function calcule_rendement(MoteurRendement $moteur, Simulation $simulation): self
    {
        $this->systemes->calcule_rendement($moteur, $simulation);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur, Simulation $simulation): self
    {
        $this->systemes->calcule_consommations($moteur, $simulation);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function solaire(): ?Solaire
    {
        return $this->solaire;
    }

    public function regulation_centrale(): Regulation
    {
        return $this->regulation_centrale;
    }

    public function regulation_terminale(): Regulation
    {
        return $this->regulation_terminale;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function comptage_individuel(): bool
    {
        return $this->comptage_individuel;
    }

    public function installation_collective(): bool
    {
        return $this->systemes->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_CENTRAL)->has_generateur_collectif();
    }

    public function effet_joule(): bool
    {
        return $this->systemes->effet_joule();
    }

    public function systemes(): SystemeCollection
    {
        return $this->systemes;
    }

    public function add_systeme(Systeme $entity): self
    {
        $this->systemes->add($entity);
        $this->controle();
        return $this;
    }

    public function remove_systeme(Systeme $entity): self
    {
        $this->systemes->remove($entity);
        return $this;
    }

    public function rdim(): float
    {
        return $this->rdim;
    }

    public function consommations(): ConsommationCollection
    {
        return $this->consommations;
    }

    // * helpers

    public function emetteurs(): EmetteurCollection
    {
        return EmetteurCollection::fromCollections(...$this->systemes()->map(fn(Systeme $systeme) => $systeme->emetteurs())->values());
    }
}
