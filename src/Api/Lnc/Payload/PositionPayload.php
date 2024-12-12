<?php

namespace App\Api\Lnc\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Enum\Mitoyennete;
use App\Domain\Lnc\ValueObject\Position;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionPayload
{
    public function __construct(
        public Mitoyennete $mitoyennete,
        #[Assert\Uuid]
        public string $paroi_id,
        #[AppAssert\Orientation]
        public ?float $orientation,
    ) {}

    public function to(): Position
    {
        return $this->paroi_id
            ? Position::create_liaison_paroi(paroi_id: Id::from($this->paroi_id), orientation: $this->orientation,)
            : Position::create(mitoyennete: $this->mitoyennete, orientation: $this->orientation,);
    }
}
