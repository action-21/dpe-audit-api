<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Service\MoteurPerformance;
use App\Domain\Refroidissement\ValueObject\{Performance, Signaletique};
use Webmozart\Assert\Assert;

/**
 * TODO: Méthode travaux
 */
final class Generateur
{
    private ?Performance $performance = null;

    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private string $description,
        private Signaletique $signaletique,
        private ?int $annee_installation,
        private ?Id $reseau_froid_id,
    ) {}

    public function controle(): void
    {
        Assert::nullOrLessThanEq($this->annee_installation, (int) date('Y'));
        Assert::nullOrGreaterThanEq($this->annee_installation, $this->refroidissement->annee_construction_batiment());
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
        return $this->signaletique->type_generateur;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->signaletique->energie_generateur;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function seer(): ?float
    {
        return $this->signaletique->seer;
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }

    public function reseau_froid_id(): ?Id
    {
        return $this->reseau_froid_id;
    }

    public function reference_reseau_froid(Id $reseau_froid_id): self
    {
        if ($this->type_generateur() === TypeGenerateur::RESEAU_FROID) {
            $this->reseau_froid_id = $reseau_froid_id;
            $this->reinitialise();
        }
        return $this;
    }

    public function dereference_reseau_froid(): self
    {
        $this->reseau_froid_id = null;
        $this->reinitialise();
        return $this;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }
}
