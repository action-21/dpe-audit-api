<?php

namespace App\Api\Lnc\Payload;

use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionWithParoiPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $paroi_id,
        #[AppAssert\Orientation]
        public ?float $orientation,
    ) {}
}
