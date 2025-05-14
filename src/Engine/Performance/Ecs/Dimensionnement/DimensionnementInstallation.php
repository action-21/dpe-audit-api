<?php

namespace App\Engine\Performance\Ecs\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Ecs\Entity\Installation;
use App\Engine\Performance\Rule;

final class DimensionnementInstallation extends Rule
{
    private Installation $installation;

    /**
     * Ratio de dimensionnement de l'installation d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        $surface_totale = $this->installation->ecs()->installations()->surface();
        $surface_couverte = $this->installation->surface();
        return $surface_couverte / $surface_totale;
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
