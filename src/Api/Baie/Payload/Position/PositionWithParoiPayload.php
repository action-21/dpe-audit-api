<?php

namespace App\Api\Baie\Payload\Position;

use App\Domain\Common\Type\Id;
use App\Domain\Baie\ValueObject\Position;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionWithParoiPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $paroi_id,
    ) {}

    public function to(): Position
    {
        return Position::create_liaison_paroi(
            paroi_id: Id::from($this->paroi_id),
        );
    }
}
