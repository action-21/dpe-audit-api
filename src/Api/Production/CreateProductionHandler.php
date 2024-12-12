<?php

namespace App\Api\Production;

use App\Api\Production\Payload\ProductionPayload;
use App\Domain\Audit\Audit;
use App\Domain\Common\Type\Id;
use App\Domain\Production\Entity\PanneauPhotovoltaique;
use App\Domain\Production\Production;

final class CreateProductionHandler
{
    public function __invoke(ProductionPayload $payload, Audit $audit): Production
    {
        $production = Production::create(audit: $audit);

        foreach ($payload->panneaux_photovoltaiques as $panneau_photovotaique_payload) {
            $production->add_panneau_photovoltaique(PanneauPhotovoltaique::create(
                id: Id::from($panneau_photovotaique_payload->id),
                production: $production,
                orientation: $panneau_photovotaique_payload->orientation,
                inclinaison: $panneau_photovotaique_payload->inclinaison,
                modules: $panneau_photovotaique_payload->modules,
                surface_capteurs: $panneau_photovotaique_payload->surface_capteurs,
            ));
        }
        return $production;
    }
}
