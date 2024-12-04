<?php

namespace App\Api\Lnc\Payload;

use App\Domain\Lnc\Enum\EtatIsolation;
use Symfony\Component\Validator\Constraints as Assert;

final class ParoiPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public PositionPayload $position,
        #[Assert\Positive]
        public float $surface,
        public EtatIsolation $etat_isolation,
    ) {}
}
