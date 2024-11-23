<?php

namespace App\Domain\Eclairage;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Eclairage\Service\MoteurConsommation;

final class Eclairage
{
    private ?ConsommationCollection $consommations = null;

    public function __construct(private readonly Audit $audit) {}

    public function reinitialise(): void
    {
        $this->consommations = null;
    }

    public function controle(): void {}

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->consommations = $moteur->calcule_consommations($this);
        return $this;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->zone_climatique();
    }

    public function consommations(): ?ConsommationCollection
    {
        return $this->consommations;
    }
}
