<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Entity\Lnc\ParoiOpaque as Entity;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionParoiOpaque
{
    public function __construct(
        #[Assert\Positive]
        public float $surface,

        public Mitoyennete $mitoyennete,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            surface: $entity->position()->surface,
            mitoyennete: $entity->position()->mitoyennete,
        );
    }
}
