<?php

namespace App\Api\Lnc\Payload;

use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class BaiePolycarbonatePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        private string $description,
        private PositionPayload|PositionWithParoiPayload $position,
        #[Assert\Positive]
        private float $surface,
        #[AppAssert\Inclinaison]
        private float $inclinaison,
    ) {}
}
