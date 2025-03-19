<?php

namespace App\Api\PlancherHaut;

use App\Api\PlancherHaut\Payload\PlancherHautPayload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherHaut\PlancherHaut;

final class CreatePlancherHautHandler
{
    public function __invoke(PlancherHautPayload $payload, Enveloppe $enveloppe,): PlancherHaut
    {
        return PlancherHaut::create(
            id: Id::from($payload->id),
            enveloppe: $enveloppe,
            description: $payload->description,
            position: $payload->position->to(),
            caracteristique: $payload->caracteristique->to(),
            isolation: $payload->isolation->to(),
        );
    }
}
