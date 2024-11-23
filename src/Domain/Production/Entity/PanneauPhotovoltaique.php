<?php

namespace App\Domain\Production\Entity;

use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Production\Production;
use App\Domain\Production\Service\MoteurProduction;
use App\Domain\Production\ValueObject\ProductionPhotovoltaiqueCollection;

final class PanneauPhotovoltaique
{
    private ?ProductionPhotovoltaiqueCollection $productions = null;

    public function __construct(
        private readonly Id $id,
        private readonly Production $production,
        private float $orientation,
        private float $inclinaison,
        private int $modules,
        private ?float $surface_capteurs,
    ) {}

    public function update(
        float $orientation,
        float $inclinaison,
        int $modules,
        ?float $surface_capteurs,
    ): self {
        $this->orientation = $orientation;
        $this->inclinaison = $inclinaison;
        $this->modules = $modules;
        $this->surface_capteurs = $surface_capteurs;
        $this->controle();
        return $this;
    }

    public function controle(): void
    {
        Assert::positif($this->surface_capteurs);
        Assert::positif($this->modules);
        Assert::orientation($this->orientation);
        Assert::inclinaison($this->inclinaison);
    }

    public function reinitialise(): void
    {
        $this->productions = null;
    }

    public function calcule_production(MoteurProduction $moteur): self
    {
        $this->productions = $moteur->calcule_production_photovoltaique($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function production(): Production
    {
        return $this->production;
    }

    public function inclinaison(): float
    {
        return $this->inclinaison;
    }

    public function orientation(): float
    {
        return $this->orientation;
    }

    public function modules(): int
    {
        return $this->modules;
    }

    public function surface_capteurs(): ?float
    {
        return $this->surface_capteurs;
    }

    public function productions(): ?ProductionPhotovoltaiqueCollection
    {
        return $this->productions;
    }
}
