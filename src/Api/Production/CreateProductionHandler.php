<?php

namespace App\Api\Production;

use App\Api\Production\Handler\CreatePanneauPhotovoltaiqueHandler;
use App\Api\Production\Model\Production as Payload;
use App\Domain\Production\Production;

final class CreateProductionHandler
{
    public function __construct(
        private readonly CreatePanneauPhotovoltaiqueHandler $panneau_photovoltaique_handler,
    ) {}

    public function __invoke(Payload $payload): Production
    {
        $entity = Production::create();

        foreach ($payload->panneaux_photovoltaiques as $panneau_photovoltaique) {
            $handle = $this->panneau_photovoltaique_handler;
            $entity->add_panneau_photovoltaique($handle(payload: $panneau_photovoltaique, production: $entity));
        }

        return $entity;
    }
}
