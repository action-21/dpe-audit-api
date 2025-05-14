<?php

namespace App\Engine\Performance\Chauffage\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Installation;
use App\Engine\Performance\Rule;

final class DimensionnementInstallation extends Rule
{
    private Installation $installation;

    /**
     * Ratio de dimensionnement de l'installation
     */
    public function rdim(): float
    {
        $surface = $this->installation->surface();
        $surface_totale = $this->installation->chauffage()->installations()->surface();
        return $surface / $surface_totale;
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
