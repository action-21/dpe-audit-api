<?php

namespace App\Api\Refroidissement\Handler;

use App\Api\Refroidissement\Model\Installation as Payload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Entity\Installation;
use App\Domain\Refroidissement\Refroidissement;

final class CreateInstallationHandler
{
    public function __invoke(Payload $payload, Refroidissement $entity): Installation
    {
        return Installation::create(
            id: Id::from($payload->id),
            refroidissement: $entity,
            description: $payload->description,
            surface: $payload->surface,
        );
    }
}
