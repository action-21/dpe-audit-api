<?php

namespace App\Api\Enveloppe\Payload;

use App\Api\Baie\Payload\BaiePayload;
use App\Api\Lnc\Payload\LncPayload;
use App\Api\Mur\Payload\MurPayload;
use App\Api\PlancherBas\Payload\PlancherBasPayload;
use App\Api\PlancherHaut\Payload\PlancherHautPayload;
use App\Api\PontThermique\Payload\PontThermiquePayload;
use App\Api\Porte\Payload\PortePayload;
use Symfony\Component\Validator\Constraints as Assert;

final class EnveloppePayload
{
    public function __construct(
        /** @var LncPayload[] */
        #[Assert\All([new Assert\Type(LncPayload::class)])]
        #[Assert\Valid]
        public array $locaux_non_chauffes = [],

        /** @var MurPayload[] */
        #[Assert\All([new Assert\Type(MurPayload::class)])]
        #[Assert\Valid]
        public array $murs = [],

        /** @var PlancherBasPayload[] */
        #[Assert\All([new Assert\Type(PlancherBasPayload::class)])]
        #[Assert\Valid]
        public array $planchers_bas = [],

        /** @var PlancherHautPayload[] */
        #[Assert\All([new Assert\Type(PlancherHautPayload::class)])]
        #[Assert\Valid]
        public array $planchers_hauts = [],

        /** @var BaiePayload[] */
        #[Assert\All([new Assert\Type(BaiePayload::class)])]
        #[Assert\Valid]
        public array $baies = [],

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
