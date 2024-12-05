<?php

namespace App\Api\Porte\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class PortePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public PositionPayload|PositionWithParoiPayload $position,
        #[Assert\Valid]
        public CaracteristiquePayload $caracteristique,
    ) {}
}
