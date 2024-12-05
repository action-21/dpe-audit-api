<?php

namespace App\Api\Ecs\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class StockagePayload
{
    public function __construct(
        #[Assert\Positive]
        public int $volume_stockage,
        public bool $position_volume_chauffe,
    ) {}
}
