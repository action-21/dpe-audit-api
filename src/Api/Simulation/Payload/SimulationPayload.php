<?php

namespace App\Api\Simulation\Payload;

use Api\Enveloppe\Payload\EnveloppePayload;
use Symfony\Component\Validator\Constraints as Assert;

final class SimulationPayload
{
    public function __construct(
        #[Assert\Valid]
        public EnveloppePayload $enveloppe,
    ) {}
}
