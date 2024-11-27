<?php

namespace App\Domain\Production;

use App\Domain\Audit\Audit;
use App\Domain\Common\Type\Id;
use App\Domain\Production\Entity\{PanneauPhotovoltaique, PanneauPhotovoltaiqueCollection};
use App\Domain\Production\Service\MoteurProduction;
use App\Domain\Simulation\Simulation;

/**
 * @see App\Domain\Audit\Audit::production()
 */
final class Production
{
    public function __construct(
        private readonly Audit $audit,
        private PanneauPhotovoltaiqueCollection $panneaux_photovoltaiques,
    ) {}

    public static function create(Audit $audit): self
    {
        return new self(
            audit: $audit,
            panneaux_photovoltaiques: new PanneauPhotovoltaiqueCollection(),
        );
    }

    public function controle(): void
    {
        $this->panneaux_photovoltaiques->controle();
    }

    public function reinitialise(): void
    {
        $this->panneaux_photovoltaiques->reinitialise();
    }

    public function calcule_production(MoteurProduction $moteur, Simulation $simulation): self
    {
        $this->panneaux_photovoltaiques->calcule_production($moteur, $simulation);
        return $this;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function panneaux_photovoltaiques(): PanneauPhotovoltaiqueCollection
    {
        return $this->panneaux_photovoltaiques;
    }

    public function panneau_photovoltaique(Id $id): ?PanneauPhotovoltaique
    {
        return $this->panneaux_photovoltaiques->find($id);
    }

    public function add_panneau_photovoltaique(PanneauPhotovoltaique $panneau): self
    {
        $this->panneaux_photovoltaiques->add($panneau);
        return $this;
    }

    public function remove_panneau_photovoltaique(PanneauPhotovoltaique $panneau): self
    {
        $this->panneaux_photovoltaiques->remove($panneau);
        return $this;
    }
}
