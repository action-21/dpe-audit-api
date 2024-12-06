<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerte, MoteurRendement};
use App\Domain\Ecs\ValueObject\Solaire;
use App\Domain\Simulation\Simulation;
use Webmozart\Assert\Assert;

/**
 * TODO: Associer l'installation à un logement ou plusieurs logements visités
 */
final class Installation
{
    private ?float $rdim = null;

    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private string $description,
        private float $surface,
        private ?Solaire $solaire,
        private SystemeCollection $systemes,
    ) {}

    public function controle(): void
    {
        Assert::greaterThan($this->surface, 0);
        Assert::nullOrGreaterThanEq($this->solaire->annee_installation, $this->ecs->annee_construction_batiment());
    }

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->systemes->reinitialise();
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->rdim = $moteur->calcule_dimensionnement_installation($this);
        $this->systemes->calcule_dimensionnement($moteur);
        return $this;
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        $this->systemes->calcule_pertes($moteur, $simulation);
        return $this;
    }

    public function calcule_rendement(MoteurRendement $moteur): self
    {
        $this->systemes->calcule_rendement($moteur);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->systemes->calcule_consommations($moteur);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ecs(): Ecs
    {
        return $this->ecs;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function solaire(): ?Solaire
    {
        return $this->solaire;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }

    public function systemes(): SystemeCollection
    {
        return $this->systemes;
    }

    public function add_systeme(Systeme $entity): self
    {
        $this->systemes->add($entity);
        return $this;
    }
}
