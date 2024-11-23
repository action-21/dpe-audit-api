<?php

namespace App\Domain\Production;

use App\Domain\Audit\Audit;
use App\Domain\Production\Entity\{PanneauPhotovoltaiqueCollection};

final class ProductionFactory
{
    public function build(Audit $audit): Production
    {
        return new Production(
            audit: $audit,
            panneaux_photovoltaiques: new PanneauPhotovoltaiqueCollection(),
        );
    }
}
