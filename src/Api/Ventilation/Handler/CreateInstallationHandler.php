<?php

namespace App\Api\Ventilation\Handler;

use App\Api\Ventilation\Model\Installation as Payload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Entity\Installation;
use App\Domain\Ventilation\Ventilation;

final class CreateInstallationHandler
{
    public function __invoke(Payload $payload, Ventilation $entity): Installation
    {
        return Installation::create(
            id: Id::from($payload->id),
            ventilation: $entity,
            description: $payload->description,
            surface: $payload->surface,
        );
    }
}
