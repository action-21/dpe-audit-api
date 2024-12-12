<?php

namespace App\Api\PlancherBas;

use App\Api\PlancherBas\Payload\PlancherBasPayload;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherBas\PlancherBas;

final class CreatePlancherBasHandler
{
    public function __invoke(PlancherBasPayload $payload, Enveloppe $enveloppe,): PlancherBas
    {
        return PlancherBas::create(
            id: Id::from($payload->id),
            enveloppe: $enveloppe,
            description: $payload->description,
            position: $payload->position->to(),
            caracteristique: $payload->caracteristique->to(),
            isolation: $payload->isolation->to(),
        );
    }
}
