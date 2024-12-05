<?php

namespace App\Api\Simulation\Payload;

use Api\Enveloppe\Payload\EnveloppePayload;
use App\Api\Refroidissement\Payload\RefroidissementPayload;
use App\Api\Ventilation\Payload\VentilationPayload;
use Symfony\Component\Validator\Constraints as Assert;

final class SimulationPayload
{
    public function __construct(
        #[Assert\Valid]
        public EnveloppePayload $enveloppe,
        #[Assert\Valid]
        public VentilationPayload $ventilation,
        #[Assert\Valid]
        public RefroidissementPayload $refroidissement,
    ) {}
}
