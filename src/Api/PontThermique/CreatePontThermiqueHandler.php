<?php

namespace App\Api\PontThermique;

use App\Api\PontThermique\Payload\PontThermiquePayload;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PontThermique\PontThermique;

final class CreatePontThermiqueHandler
{
    public function __invoke(PontThermiquePayload $payload, Enveloppe $enveloppe,): PontThermique
    {
        return PontThermique::create(
            id: Id::from($payload->id),
            enveloppe: $enveloppe,
            description: $payload->description,
            longueur: $payload->longueur,
            liaison: $payload->liaison->to(),
            kpt: $payload->kpt,
        );
    }
}
