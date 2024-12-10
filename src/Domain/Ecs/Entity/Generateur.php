<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, UsageEcs};
use App\Domain\Ecs\Service\{MoteurPerformance, MoteurPerte};
use App\Domain\Ecs\ValueObject\{Combustion, Performance, PerteCollection, Signaletique};
use App\Domain\Simulation\Simulation;
use Webmozart\Assert\Assert;

final class Generateur
{
    private ?Performance $performance = null;
    private ?PerteCollection $pertes_generation = null;
    private ?PerteCollection $pertes_stockage = null;

    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private string $description,
        private ?Id $generateur_mixte_id,
        private ?Id $reseau_chaleur_id,
        private ?int $annee_installation,
        private bool $position_volume_chauffe,
        private bool $generateur_collectif,
        private Signaletique $signaletique,
    ) {}

    public function controle(): void
    {
        Assert::nullOrLessThanEq($this->annee_installation, (int) date('Y'));
        Assert::nullOrGreaterThanEq($this->annee_installation, $this->ecs->annee_construction_batiment());
    }

    public function reinitialise(): void
    {
        $this->performance = null;
        $this->pertes_generation = null;
        $this->pertes_stockage = null;
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        $this->performance = $moteur->calcule_performance($this, $simulation);
        return $this;
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        $this->pertes_generation = $moteur->calcule_pertes_generation($this, $simulation);
        $this->pertes_stockage = $moteur->calcule_pertes_stockage_generateur($this, $simulation);
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

    public function type(): TypeGenerateur
    {
        return $this->signaletique->type;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->signaletique->energie;
    }

    public function volume_stockage(): int
    {
        return $this->signaletique->volume_stockage;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
    }

    public function generateur_collectif(): bool
    {
        return $this->generateur_collectif;
    }

    public function combustion(): ?Combustion
    {
        return $this->signaletique->combustion;
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }

    public function usage(): UsageEcs
    {
        return $this->generateur_mixte_id ? UsageEcs::CHAUFFAGE_ECS : UsageEcs::ECS;
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
        if (\in_array($this->signaletique->type, [
            TypeGenerateur::CHAUDIERE,
            TypeGenerateur::CHAUDIERE_MULTI_BATIMENT,
            TypeGenerateur::PAC_DOUBLE_SERVICE,
            TypeGenerateur::PAC_MULTI_BATIMENT,
            TypeGenerateur::POELE_BOUILLEUR,
            TypeGenerateur::RESEAU_CHALEUR,
        ])) $this->generateur_mixte_id = $generateur_mixte_id;

        $this->reinitialise();
        return $this;
    }

    public function dereference_generateur_mixte(): self
    {
        $this->generateur_mixte_id = null;
        $this->reinitialise();
        return $this;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function pertes_generation(): ?PerteCollection
    {
        return $this->pertes_generation;
    }

    public function pertes_stockage(): ?PerteCollection
    {
        return $this->pertes_stockage;
    }
}
