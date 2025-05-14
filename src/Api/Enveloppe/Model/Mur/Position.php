<?php

namespace App\Api\Enveloppe\Model\Mur;

use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Domain\Enveloppe\Entity\Mur as Entity;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Position
{
    public function __construct(
        public ?string $local_non_chauffe_id,

        #[Assert\Positive]
        public float $surface,

        #[DpeAssert\Orientation]
        public ?float $orientation,

        public Mitoyennete $mitoyennete,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            local_non_chauffe_id: $entity->position()->local_non_chauffe?->id(),
            surface: $entity->position()->surface,
            orientation: $entity->position()->orientation?->value,
            mitoyennete: $entity->position()->mitoyennete,
        );
    }
}
