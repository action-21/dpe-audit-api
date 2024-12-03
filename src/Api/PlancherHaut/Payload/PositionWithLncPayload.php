<?php

namespace App\Api\PlancherHaut\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\PlancherHaut\ValueObject\Position;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionWithLncPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $local_non_chauffe_id,
        #[AppAssert\Orientation]
        public ?float $orientation,
    ) {}

    public function to(): Position
    {
        return Position::create_liaison_local_non_chauffe(
            local_non_chauffe_id: Id::from($this->local_non_chauffe_id),
            orientation: $this->orientation,
        );
    }
}
