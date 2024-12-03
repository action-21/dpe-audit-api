<?php

namespace App\Api\Porte\Payload;

use App\Domain\Porte\Enum\Mitoyennete;
use App\Domain\Porte\ValueObject\Position;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\NotEqualTo(Mitoyennete::LOCAL_NON_CHAUFFE)]
        public Mitoyennete $mitoyennete,

        #[Assert\NotBlank]
        #[AppAssert\Orientation]
        public ?float $orientation,
    ) {}

    public function to(): Position
    {
        return Position::create(
            mitoyennete: $this->mitoyennete,
            orientation: $this->orientation,
        );
    }
}
