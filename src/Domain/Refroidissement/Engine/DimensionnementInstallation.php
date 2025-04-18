<?php

namespace App\Domain\Refroidissement\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Refroidissement\Entity\Installation;

final class DimensionnementInstallation extends EngineRule
{
    private Installation $installation;

    /**
     * Ratio de dimensionnement de l'installation de refroidissement
     */
    public function rdim(): float
    {
        $surface_ventilee = $this->installation->refroidissement()->installations()->surface();
        $surface_installation = $this->installation->surface();
        return $surface_ventilee / $surface_installation;
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
