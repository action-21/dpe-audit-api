<?php

namespace App\Api\Porte;

use App\Api\Porte\Payload\PortePayload;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Porte\Porte;

final class CreatePorteHandler
{
    public function __invoke(PortePayload $payload, Enveloppe $enveloppe,): Porte
    {
        return Porte::create(
            id: Id::from($payload->id),
            enveloppe: $enveloppe,
            description: $payload->description,
            position: $payload->position->to(),
            caracteristique: $payload->caracteristique->to(),
        );
    }
}
