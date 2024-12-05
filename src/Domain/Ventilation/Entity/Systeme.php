<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeSysteme};
use App\Domain\Ventilation\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};
use App\Domain\Ventilation\ValueObject\Performance;
use App\Domain\Ventilation\Ventilation;

final class Systeme
{
    private ?float $rdim = null;
    private ?Performance $performance = null;
    private ?ConsommationCollection $consommations = null;

    public function __construct(
        private readonly Id $id,
        private readonly Installation $installation,
        private TypeSysteme $type,
        private ?Generateur $generateur,
        private ?ModeExtraction $mode_extraction,
        private ?ModeInsufflation $mode_insufflation,
    ) {}

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->performance = null;
        $this->consommations = null;
    }

    public function controle(): void {}

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->rdim = $moteur->calcule_dimensionnement_systeme($this);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur->calcule_performance_systeme($this);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->consommations = $moteur->calcule_consommations($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->installation->ventilation();
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): ?Generateur
    {
        return $this->generateur;
    }

    public function type(): TypeSysteme
    {
        return $this->type;
    }

    public function mode_extraction(): ?ModeExtraction
    {
        return $this->mode_extraction;
    }

    public function mode_insufflation(): ?ModeInsufflation
    {
        return $this->mode_insufflation;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function consommations(): ?ConsommationCollection
    {
        return $this->consommations;
    }
}
