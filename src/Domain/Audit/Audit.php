<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Service\{MoteurDimensionnement, MoteurScenarioConventionnel};
use App\Domain\Audit\ValueObject\{Adresse, Batiment, Logement, Occupation, Situation};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\Simulation;

final class Audit
{
    private ?float $ratio_virtualisation = null;
    private ?Occupation $occupation = null;
    private ?Situation $situation = null;

    public function __construct(
        public readonly Id $id,
        public readonly \DateTimeImmutable $date_etablissement,
        public readonly Adresse $adresse,
        public readonly Batiment $batiment,
        public readonly ?Logement $logement,
    ) {}

    public static function create(Adresse $adresse, Batiment $batiment, ?Logement $logement): self
    {
        $entity = new self(
            id: Id::create(),
            date_etablissement: new \DateTimeImmutable(),
            adresse: $adresse,
            batiment: $batiment,
            logement: $logement,
        );

        $entity->controle();
        return $entity;
    }

    public function controle(): void
    {
        $this->adresse->controle();
        $this->batiment->controle();
    }

    public function reinitialise(): void
    {
        $this->ratio_virtualisation = null;
        $this->occupation = null;
        $this->situation = null;
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->ratio_virtualisation = $moteur->calcule_dimensionnement($this);
        return $this;
    }

    public function calcule_occupation(MoteurScenarioConventionnel $moteur): self
    {
        $this->occupation = $moteur->calcule_occupation($this);
        return $this;
    }

    public function calcule_situation(MoteurScenarioConventionnel $moteur, Simulation $simulation): self
    {
        $this->situation = $moteur->calcule_situation($this, $simulation);
        return $this;
    }

    public function occupation(): ?Occupation
    {
        return $this->occupation;
    }

    public function situation(): ?Situation
    {
        return $this->situation;
    }

    public function ratio_virtualisation(): ?float
    {
        return $this->ratio_virtualisation;
    }

    public function ratio_proratisation(): ?float
    {
        return $this->ratio_virtualisation;
    }

    // * helpers

    public function altitude(): int
    {
        return $this->batiment->altitude;
    }

    public function nombre_logements(): int
    {
        return $this->batiment->logements;
    }
}
