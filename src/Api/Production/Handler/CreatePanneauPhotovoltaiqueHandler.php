<?php

namespace App\Api\Production\Handler;

use App\Api\Production\Model\PanneauPhotovoltaique as Payload;
use App\Domain\Common\ValueObject\{Id, Inclinaison, Orientation};
use App\Domain\Production\Entity\PanneauPhotovoltaique;
use App\Domain\Production\Production;

final class CreatePanneauPhotovoltaiqueHandler
{
    public function __invoke(Payload $payload, Production $production): PanneauPhotovoltaique
    {
        return PanneauPhotovoltaique::create(
            id: Id::from($payload->id),
            production: $production,
            description: $payload->description,
            orientation: Orientation::from($payload->orientation),
            inclinaison: Inclinaison::from($payload->inclinaison),
            modules: $payload->modules,
            surface: $payload->surface,
        );
    }
}
