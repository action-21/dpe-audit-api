<?php

namespace App\Api\Simulation\Payload;

use Api\Enveloppe\Payload\CreateEnveloppePayload;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateSimulationPayload
{
    public function __construct(
        #[Assert\Valid]
        public CreateEnveloppePayload $enveloppe,
    ) {}
}
