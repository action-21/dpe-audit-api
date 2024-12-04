<?php

namespace App\Api\Lnc\Payload;

use App\Domain\PlancherHaut\Enum\Mitoyennete;
use App\Services\Validator\Constraints as AppAssert;

final class PositionPayload
{
    public function __construct(
        public Mitoyennete $mitoyennete,
        #[AppAssert\Orientation]
        public ?float $orientation,
    ) {}
}
