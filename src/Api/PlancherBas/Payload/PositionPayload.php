<?php

namespace App\Api\PlancherBas\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\PlancherBas\Enum\Mitoyennete;
use App\Domain\PlancherBas\ValueObject\Position;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionPayload
{
    public function __construct(
        public Mitoyennete $mitoyennete,
        #[Assert\Uuid]
        public ?string $local_non_chauffe_id,
    ) {}

    public function local_non_chauffe_id(): ?Id
    {
        return $this->local_non_chauffe_id ? Id::from($this->local_non_chauffe_id) : null;
    }

    public function to(): Position
    {
        return Position::create(mitoyennete: $this->mitoyennete, local_non_chauffe_id: $this->local_non_chauffe_id());
    }
}
