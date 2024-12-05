<?php

namespace App\Api\PlancherHaut\Payload;

use App\Domain\Common\Type\Id;
use Symfony\Component\Validator\Constraints as Assert;

final class PlancherHautPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public PositionPayload $position,
        #[Assert\Valid]
        public CaracteristiquePayload $caracteristique,
        #[Assert\Valid]
        public IsolationPayload $isolation,
    ) {}

    public function id(): Id
    {
        return Id::from($this->id);
    }
}
