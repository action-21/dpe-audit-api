<?php

namespace App\Api\PlancherBas\Payload;

use App\Domain\PlancherBas\Enum\Mitoyennete;
use App\Domain\PlancherBas\ValueObject\Position;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionPayload
{
    public function __construct(
        #[Assert\NotEqualTo(Mitoyennete::LOCAL_NON_CHAUFFE)]
        public Mitoyennete $mitoyennete,
    ) {}

    public function to(): Position
    {
        return Position::create(mitoyennete: $this->mitoyennete);
    }
}
