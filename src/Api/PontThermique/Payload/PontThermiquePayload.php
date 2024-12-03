<?php

namespace App\Api\PontThermique\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class PontThermiquePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public float $longueur,
        public LiaisonPlancherBasMurPayload|LiaisonPlancherIntermediaireMurPayload|LiaisonPlancherHautMurPayload|LiaisonRefendMurPayload|LiaisonMenuiserieMurPayload $liaison,
        #[Assert\Positive]
        public ?float $kpt,
    ) {}
}
