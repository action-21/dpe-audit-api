<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie as Entity;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Position
{
    public function __construct(
        public ?string $paroi_id,

        public ?string $local_non_chauffe_id,

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
            paroi_id: $entity->position()->paroi?->id(),
            local_non_chauffe_id: $entity->position()->local_non_chauffe?->id(),
            surface: $entity->position()->surface,
            inclinaison: $entity->position()->inclinaison->value,
            orientation: $entity->position()->orientation?->value,
            mitoyennete: $entity->position()->mitoyennete,
        );
    }
}
