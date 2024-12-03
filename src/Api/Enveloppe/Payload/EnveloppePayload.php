<?php

namespace Api\Enveloppe\Payload;

use App\Api\PlancherHaut\Payload\PlancherHautPayload;
use App\Api\PontThermique\Payload\PontThermiquePayload;
use App\Api\Porte\Payload\PortePayload;
use Symfony\Component\Validator\Constraints as Assert;

final class EnveloppePayload
{
    public function __construct(
        /** @var PlancherHautPayload[] */
        #[Assert\All([new Assert\Type(PlancherHautPayload::class)])]
        #[Assert\Valid]
        public array $planchers_hauts = [],

        /** @var PortePayload[] */
        #[Assert\All([new Assert\Type(PortePayload::class)])]
        #[Assert\Valid]
        public array $portes = [],

        /** @var PontThermiquePayload[] */
        #[Assert\All([new Assert\Type(PontThermiquePayload::class)])]
        #[Assert\Valid]
        public array $ponts_thermiques = [],
    ) {}
}
