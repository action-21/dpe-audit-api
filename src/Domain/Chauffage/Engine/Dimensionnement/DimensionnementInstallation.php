<?php

namespace App\Domain\Chauffage\Engine\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Installation;
use App\Domain\Common\EngineRule;

final class DimensionnementInstallation extends EngineRule
{
    private Installation $installation;

    /**
     * Ratio de dimensionnement de l'installation
     */
    public function rdim(): float
    {
        $surface_couverte = $this->installation->chauffage()->installations()->surface();
        $surface_installation = $this->installation->surface();
        return $surface_couverte / $surface_installation;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->installations() as $installation) {
            $this->installation = $installation;
            $installation->calcule($installation->data()->with(
                rdim: $this->rdim(),
            ));
        }
    }
}
