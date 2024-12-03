<?php

namespace App\Api\PontThermique\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class PontThermiquePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Positive]
        public float $longueur,
        #[Assert\Valid]
        public LiaisonPlancherBasMurPayload|LiaisonPlancherIntermediaireMurPayload|LiaisonPlancherHautMurPayload|LiaisonRefendMurPayload|LiaisonMenuiserieMurPayload $liaison,
        #[Assert\Positive]
        public ?float $kpt,
    ) {}
}
