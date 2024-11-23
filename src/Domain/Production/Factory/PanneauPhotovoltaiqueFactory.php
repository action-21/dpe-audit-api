<?php

namespace App\Domain\Production\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Production\Entity\PanneauPhotovoltaique;
use App\Domain\Production\Production;

final class PanneauPhotovoltaiqueFactory
{
    public function build(
        Id $id,
        Production $production,
        float $orientation,
        float $inclinaison,
        int $modules,
        ?float $surface_capteurs,
    ): PanneauPhotovoltaique {
        $entity = new PanneauPhotovoltaique(
            id: $id,
            production: $production,
            orientation: $orientation,
            inclinaison: $inclinaison,
            modules: $modules,
            surface_capteurs: $surface_capteurs,
        );
        $entity->controle();
        return $entity;
    }
}
