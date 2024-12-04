<?php

namespace App\Api\PlancherBas\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class PlancherBasPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public PositionPayload|PositionWithLncPayload $position,
        #[Assert\Valid]
        public CaracteristiquePayload $caracteristique,
        #[Assert\Valid]
        public IsolationPayload $isolation,
    ) {}
}
