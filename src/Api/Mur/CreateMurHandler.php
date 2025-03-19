<?php

namespace App\Api\Mur;

use App\Api\Mur\Payload\MurPayload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Mur\Mur;

final class CreateMurHandler
{
    public function __invoke(MurPayload $payload, Enveloppe $enveloppe,): Mur
    {
        return Mur::create(
            id: Id::from($payload->id),
            enveloppe: $enveloppe,
            description: $payload->description,
            position: $payload->position->to(),
            caracteristique: $payload->caracteristique->to(),
            isolation: $payload->isolation->to(),
        );
    }
}
