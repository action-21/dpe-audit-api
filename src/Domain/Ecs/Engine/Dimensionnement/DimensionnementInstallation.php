<?php

namespace App\Domain\Ecs\Engine\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Ecs\Entity\Installation;

final class DimensionnementInstallation extends EngineRule
{
    private Installation $installation;

    /**
     * Ratio de dimensionnement de l'installation d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        $surface_couverte = $this->installation->ecs()->installations()->surface();
        $surface_installation = $this->installation->surface();
        return $surface_couverte / $surface_installation;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ecs()->installations() as $installation) {
            $this->installation = $installation;
            $installation->calcule($installation->data()->with(
                rdim: $this->rdim()
            ));
        }
    }
}
