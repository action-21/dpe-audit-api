<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Audit\Service\{MoteurDimensionnement, MoteurScenarioConventionnel};
use App\Domain\Audit\ValueObject\{Adresse, Batiment, Logement, Occupation, Situation};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Type\Id;
use App\Domain\Simulation\Simulation;

final class Audit
{
    private ?float $ratio_virtualisation = null;
    private ?Occupation $occupation = null;
    private ?Situation $situation = null;

    public function __construct(
        private readonly Id $id,
        private readonly \DateTimeImmutable $date_creation,
        private Adresse $adresse,
        private Batiment $batiment,
        private ?Logement $logement,
    ) {}

    public function update(Adresse $adresse, Batiment $batiment, ?Logement $logement): self
    {
        $this->adresse = $adresse;
        $this->batiment = $batiment;
        $this->logement = $logement;
        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function controle(): void
    {
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

    public function id(): Id
    {
        return $this->id;
    }

    public function date_creation(): \DateTimeImmutable
    {
        return $this->date_creation;
    }

    public function adresse(): Adresse
    {
        return $this->adresse;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
    }

    public function logement(): ?Logement
    {
        return $this->logement;
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

    public function zone_climatique(): ZoneClimatique
    {
        return $this->adresse->zone_climatique;
    }

    public function type_batiment(): TypeBatiment
    {
        return $this->batiment->type;
    }

    public function altitude(): int
    {
        return $this->batiment->altitude;
    }

    public function nombre_logements(): int
    {
        return $this->batiment->logements;
    }

    public function annee_construction_batiment(): int
    {
        return $this->batiment->annee_construction;
    }

    public function surface_habitable_reference(): float
    {
        return $this->logement?->surface_habitable ?? $this->batiment->surface_habitable;
    }

    public function hauteur_sous_plafond_reference(): float
    {
        return $this->logement?->hauteur_sous_plafond ?? $this->batiment->hauteur_sous_plafond;
    }

    public function surface_habitable_moyenne(): float
    {
        return $this->batiment->surface_habitable / $this->batiment->logements;
    }
}
