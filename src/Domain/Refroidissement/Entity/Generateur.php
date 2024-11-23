<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Service\MoteurPerformance;
use App\Domain\Refroidissement\ValueObject\Performance;

final class Generateur
{
    private ?Performance $performance = null;

    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private string $description,
        private TypeGenerateur $type_generateur,
        private EnergieGenerateur $energie_generateur,
        private ?int $annee_installation,
        private ?float $seer,
    ) {}

    public function set_systeme_thermodynamique(TypeGenerateur\TypeThermodynamique $type_generateur): self
    {
        $this->type_generateur = $type_generateur->to();
        $this->energie_generateur = EnergieGenerateur::ELECTRICITE;
        $this->reinitialise();
        return $this;
    }

    public function set_reseau_froid(): self
    {
        $this->type_generateur = TypeGenerateur::RESEAU_FROID;
        $this->energie_generateur = EnergieGenerateur::RESEAU_FROID;
        $this->seer = null;
        $this->reinitialise();
        return $this;
    }

    public function set_autres_systemes(TypeGenerateur\TypeAutres $type_generateur, EnergieGenerateur $energie): self
    {
        $this->type_generateur = $type_generateur->to();
        $this->energie_generateur = $energie;
        $this->seer = null;
        $this->reinitialise();
        return $this;
    }

    public function update(string $description, ?int $annee_installation, ?float $seer,): self
    {
        $this->description = $description;
        $this->annee_installation = $annee_installation;
        $this->seer = $seer;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function controle(): void
    {
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $this->refroidissement->annee_construction_batiment());
        Assert::positif($this->seer);
    }

    public function reinitialise(): void
    {
        $this->performance = null;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur->calcule_performance($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->type_generateur;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->energie_generateur;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function seer(): ?float
    {
        return $this->seer;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }
}
