<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Entity\Lnc\Baie as Entity;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PositionBaie
{
    public function __construct(
        public ?string $paroi_id,

        #[Assert\Positive]
        public float $surface,

        #[DpeAssert\Inclinaison]
        public float $inclinaison,

        #[DpeAssert\Orientation]
        public ?float $orientation,

        public Mitoyennete $mitoyennete,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            surface: $entity->position()->surface,
            paroi_id: $entity->position()->paroi?->id(),
            inclinaison: $entity->position()->inclinaison->value,
            orientation: $entity->position()->orientation?->value,
            mitoyennete: $entity->position()->mitoyennete,
        );
    }
}
