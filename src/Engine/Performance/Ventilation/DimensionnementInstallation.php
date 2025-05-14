<?php

namespace App\Engine\Performance\Ventilation;

use App\Domain\Audit\Audit;
use App\Domain\Ventilation\Entity\Installation;
use App\Engine\Performance\Rule;

final class DimensionnementInstallation extends Rule
{
    private Installation $installation;

    /**
     * Ratio de dimensionnement de l'installation de ventilation
     */
    public function rdim(): float
    {
        $surface_ventilee = $this->installation->ventilation()->installations()->surface();
        $surface_installation = $this->installation->surface();
        return $surface_ventilee / $surface_installation;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ventilation()->installations() as $installation) {
            $this->installation = $installation;
            $installation->calcule($installation->data()->with(
                rdim: $this->rdim()
            ));
        }
    }
}
