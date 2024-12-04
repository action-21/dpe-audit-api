<?php

namespace App\Api\Lnc\Payload;

use App\Domain\Lnc\Enum\TypeBaie;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class FenetrePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        private string $description,
        private PositionPayload|PositionWithParoiPayload $position,
        private TypeBaie\TypeBaieFenetre $type,
        #[Assert\Positive]
        private float $surface,
        #[AppAssert\Inclinaison]
        private float $inclinaison,
        #[Assert\Valid]
        private MenuiseriePayload $menuiserie,
    ) {}
}
