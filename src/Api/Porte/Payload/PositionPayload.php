<?php

namespace App\Api\Porte\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\Porte\Enum\Mitoyennete;
use App\Domain\Porte\ValueObject\Position;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionPayload
{
    public function __construct(
        public Mitoyennete $mitoyennete,
        #[AppAssert\Orientation]
        public float $orientation,
        #[Assert\Uuid]
        public ?string $local_non_chauffe_id,
    ) {}

    public function local_non_chauffe_id(): ?Id
    {
        return $this->local_non_chauffe_id ? Id::from($this->local_non_chauffe_id) : null;
    }

    public function to(): Position
    {
        return Position::create(
            mitoyennete: $this->mitoyennete,
            orientation: $this->orientation,
            local_non_chauffe_id: $this->local_non_chauffe_id(),
        );
    }
}
