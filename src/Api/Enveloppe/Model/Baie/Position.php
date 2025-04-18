<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie as Entity;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Position
{
    public function __construct(
        #[Assert\Uuid]
        public readonly ?string $paroi_id,

        #[Assert\Uuid]
        public readonly ?string $local_non_chauffe_id,

        #[Assert\Positive]
        public readonly float $surface,

        #[DpeAssert\Inclinaison]
        public readonly float $inclinaison,

        #[DpeAssert\Orientation]
        public readonly ?float $orientation,

        public readonly Mitoyennete $mitoyennete,
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
