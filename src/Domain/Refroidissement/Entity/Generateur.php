<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Service\MoteurPerformance;
use App\Domain\Refroidissement\ValueObject\Performance;
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
        private TypeGenerateur $type_generateur,
        private EnergieGenerateur $energie_generateur,
        private ?int $annee_installation,
        private ?float $seer,
        private ?Id $reseau_froid_id = null,
    ) {}

    public function controle(): void
    {
        Assert::lessThanEq($this->annee_installation, (int) date('Y'));
        Assert::greaterThanEq($this->annee_installation, $this->refroidissement->annee_construction_batiment());
        Assert::greaterThan($this->seer, 0);
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

    public function reseau_froid_id(): ?Id
    {
        return $this->reseau_froid_id;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }
}
