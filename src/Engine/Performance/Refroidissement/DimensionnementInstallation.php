<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Refroidissement\Entity\Installation;
use App\Engine\Performance\Rule;

final class DimensionnementInstallation extends Rule
{
    private Installation $installation;

    /**
     * Ratio de dimensionnement de l'installation de refroidissement
     */
    public function rdim(): float
    {
        $surface_totale = $this->installation->refroidissement()->installations()->surface();
        $surface_couverte = $this->installation->surface();
        return $surface_couverte / $surface_totale;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->refroidissement()->installations() as $installation) {
            $this->installation = $installation;
            $installation->calcule($installation->data()->with(
                rdim: $this->rdim()
            ));
        }
    }
}
