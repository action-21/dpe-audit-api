<?php

namespace Api\Enveloppe\Payload;

use App\Api\PontThermique\Payload\PontThermiquePayload;
use App\Api\Porte\Payload\PortePayload;
use Symfony\Component\Validator\Constraints as Assert;

final class EnveloppePayload
{
    public function __construct(
        /** @var PortePayload[] */
        #[Assert\All([
            new Assert\Type(PortePayload::class),
            new Assert\Valid(),
        ])]
        public array $portes = [],

        /** @var PontThermiquePayload[] */
        #[Assert\All([
            new Assert\Type(PontThermiquePayload::class),
            new Assert\Valid(),
        ])]
        public array $ponts_thermiques = [],
    ) {}
}
