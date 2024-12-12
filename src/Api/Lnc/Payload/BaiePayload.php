<?php

namespace App\Api\Lnc\Payload;

use App\Domain\Lnc\Enum\TypeBaie;
use App\Services\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class BaiePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Valid]
        public PositionPayload $position,
        public TypeBaie $type,
        #[Assert\Positive]
        public float $surface,
        #[AppAssert\Inclinaison]
        public float $inclinaison,
        #[Assert\Valid]
        public ?MenuiseriePayload $menuiserie,
    ) {}

    #[Assert\IsTrue]
    public function isValid(): bool
    {
        return $this->type->is_fenetre() ? null !== $this->menuiserie : true;
    }
}
