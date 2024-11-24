<?php

namespace App\Domain\Simulation;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Chauffage;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Type\Id;
use App\Domain\Eclairage\Eclairage;
use App\Domain\Ecs\Ecs;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Production\Production;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Simulation\Service\MoteurPerformance;
use App\Domain\Simulation\ValueObject\{Bilan, PerformanceCollection};
use App\Domain\Ventilation\Ventilation;
use App\Domain\Visite\Visite;

final class Simulation
{
    private ?PerformanceCollection $performances = null;
    private ?Bilan $bilan = null;

    public function __construct(
        private readonly Id $id,
        private Audit $audit,
        private Enveloppe $enveloppe,
        private Chauffage $chauffage,
        private Ecs $ecs,
        private Refroidissement $refroidissement,
        private Ventilation $ventilation,
        private Production $production,
        private Visite $visite,
        private Eclairage $eclairage
    ) {}

    public function reinitialise(): self
    {
        $this->audit->reinitialise();
        $this->enveloppe->reinitialise();
        $this->chauffage->reinitialise();
        $this->ecs->reinitialise();
        $this->refroidissement->reinitialise();
        $this->ventilation->reinitialise();
        $this->production->reinitialise();
        $this->eclairage->reinitialise();
        $this->visite->reinitialise();

        $this->performances = null;
        return $this;
    }

    public function controle(): self
    {
        $this->audit->controle();
        $this->enveloppe->controle();
        $this->chauffage->controle();
        $this->ecs->controle();
        $this->refroidissement->controle();
        $this->ventilation->controle();
        $this->production->controle();
        $this->eclairage->controle();
        $this->visite->controle();
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performances = $moteur->calcule_performance($this);
        $this->bilan = $moteur->calcule_bilan($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function ecs(): Ecs
    {
        return $this->ecs;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function production(): Production
    {
        return $this->production;
    }

    public function visite(): Visite
    {
        return $this->visite;
    }

    public function eclairage(): Eclairage
    {
        return $this->eclairage;
    }

    public function performances(): ?PerformanceCollection
    {
        return $this->performances;
    }

    public function bilan(): ?Bilan
    {
        return $this->bilan;
    }

    // * helpers

    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->adresse()->zone_climatique;
    }

    public function effet_joule(): float
    {
        return $this->chauffage->effet_joule();
    }

    public function annee_construction_batiment(): int
    {
        return $this->audit->batiment()->annee_construction;
    }

    public function nombre_logements(): int
    {
        return $this->audit->batiment()->logements;
    }

    public function surface_habitable_reference(): float
    {
        return $this->audit->logement()?->surface_habitable ?? $this->audit->batiment()->surface_habitable;
    }

    public function hauteur_sous_plafond_reference(): float
    {
        return $this->audit->logement()?->hauteur_sous_plafond ?? $this->audit->batiment()->hauteur_sous_plafond;
    }

    public function surface_habitable_moyenne(): float
    {
        return $this->audit->batiment()->surface_habitable / $this->audit->batiment()->logements;
    }

    public function ratio_proratisation(): float
    {
        return $this->surface_habitable_reference() / $this->audit->batiment()->surface_habitable;
    }
}
