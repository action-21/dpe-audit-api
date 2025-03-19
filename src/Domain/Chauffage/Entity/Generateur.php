<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur, UsageChauffage};
use App\Domain\Chauffage\Service\{MoteurDimensionnement, MoteurPerformance, MoteurPerte};
use App\Domain\Chauffage\ValueObject\{Combustion, Performance, PerteCollection, Signaletique};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\Simulation;
use Webmozart\Assert\Assert;

final class Generateur
{
    private ?float $pch = null;
    private ?Performance $performance = null;
    private ?PerteCollection $pertes_generation = null;

    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private ?Id $generateur_mixte_id,
        private ?Id $reseau_chaleur_id,
        private string $description,
        private bool $position_volume_chauffe,
        private bool $generateur_collectif,
        private ?int $annee_installation,
        private Signaletique $signaletique,
    ) {}

    public static function create(
        Id $id,
        Chauffage $chauffage,
        string $description,
        ?Id $generateur_mixte_id,
        ?Id $reseau_chaleur_id,
        ?int $annee_installation,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        Signaletique $signaletique,
    ): self {
        Assert::nullOrLessThanEq($annee_installation, (int) date('Y'));
        Assert::nullOrGreaterThanEq($annee_installation, $chauffage->annee_construction_batiment());

        return new self(
            id: $id,
            chauffage: $chauffage,
            description: $description,
            generateur_mixte_id: $signaletique->type->is_usage_mixte() ? $generateur_mixte_id : null,
            reseau_chaleur_id: $signaletique->type->is_reseau_chaleur() ? $reseau_chaleur_id : null,
            position_volume_chauffe: $signaletique->type->position_volume_chauffe() ?? $position_volume_chauffe,
            generateur_collectif: $signaletique->type->is_generateur_collectif() ?? $generateur_collectif,
            annee_installation: $annee_installation,
            signaletique: $signaletique,
        );
    }

    public function controle(): void
    {
        Assert::nullOrLessThanEq($this->annee_installation, (int) date('Y'));
        Assert::nullOrGreaterThanEq($this->annee_installation, $this->chauffage->annee_construction_batiment());
    }

    public function reinitialise(): void
    {
        $this->pch = null;
        $this->performance = null;
        $this->pertes_generation = null;
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur, Simulation $simulation): self
    {
        $this->pch = $moteur->calcule_pch($this, $simulation);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        $this->performance = $moteur->calcule_performance($this, $simulation);
        return $this;
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        $this->pertes_generation = $moteur->calcule_pertes_generation($this, $simulation);
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

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
    }

    public function generateur_collectif(): bool
    {
        return $this->generateur_collectif;
    }

    public function type(): TypeGenerateur
    {
        return $this->signaletique->type;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->signaletique->energie;
    }

    public function effet_joule(): bool
    {
        return $this->signaletique->effet_joule();
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }

    public function combustion(): ?Combustion
    {
        return $this->signaletique->combustion;
    }

    public function usage(): UsageChauffage
    {
        return $this->generateur_mixte_id ? UsageChauffage::CHAUFFAGE_ECS : UsageChauffage::CHAUFFAGE;;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function reseau_chaleur_id(): ?Id
    {
        return $this->reseau_chaleur_id;
    }

    public function reference_reseau_chaleur(Id $reseau_chaleur_id): self
    {
        if ($this->signaletique->type === TypeGenerateur::RESEAU_CHALEUR) {
            $this->reseau_chaleur_id = $reseau_chaleur_id;
            $this->reinitialise();
        }
        return $this;
    }

    public function dereference_reseau_chaleur(): self
    {
        $this->reseau_chaleur_id = null;
        $this->reinitialise();
        return $this;
    }

    public function generateur_mixte_id(): ?Id
    {
        return $this->generateur_mixte_id;
    }

    public function reference_generateur_mixte(Id $generateur_mixte_id): self
    {
        if ($this->signaletique->type->is_usage_mixte()) {
            $this->generateur_mixte_id = $generateur_mixte_id;
            $this->reinitialise();
        }
        return $this;
    }

    public function dereference_generateur_mixte(): self
    {
        $this->generateur_mixte_id = null;
        $this->reinitialise();
        return $this;
    }

    public function pch(): ?float
    {
        return $this->pch;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function pertes_generation(): ?PerteCollection
    {
        return $this->pertes_generation;
    }
}
