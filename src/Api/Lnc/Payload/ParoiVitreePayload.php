<?php

namespace App\Api\Lnc\Payload;

use App\Api\Lnc\Payload\Position;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class ParoiVitreePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public Position\PositionPayload|Position\PositionWithParoiPayload $position,
        #[Assert\Positive]
        public float $surface,
        #[AppAssert\Inclinaison]
        public float $inclinaison,
    ) {}
}
