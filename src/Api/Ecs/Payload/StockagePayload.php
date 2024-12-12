<?php

namespace App\Api\Ecs\Payload;

use App\Domain\Ecs\ValueObject\Stockage;
use Symfony\Component\Validator\Constraints as Assert;

final class StockagePayload
{
    public function __construct(
        #[Assert\Positive]
        public int $volume_stockage,
        public bool $position_volume_chauffe,
    ) {}

    public function to(): Stockage
    {
        return Stockage::create(
            volume_stockage: $this->volume_stockage,
            position_volume_chauffe: $this->position_volume_chauffe,
        );
    }
}
