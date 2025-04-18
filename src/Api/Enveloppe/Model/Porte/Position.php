<?php

namespace App\Api\Enveloppe\Model\Porte;

use App\Domain\Enveloppe\Entity\Porte as Entity;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use Symfony\Component\Validator\Constraints as Assert;

final class Position
{
    public function __construct(
        #[Assert\Uuid]
        public readonly ?string $paroi_id,

        #[Assert\Uuid]
        public readonly ?string $local_non_chauffe_id,

        #[Assert\Positive]
        public readonly bool $surface,

        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThan(360)]
        public readonly ?float $orientation,

        public readonly Mitoyennete $mitoyennete,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            paroi_id: $entity->position()->paroi?->id(),
            local_non_chauffe_id: $entity->position()->local_non_chauffe?->id(),
            surface: $entity->position()->surface,
            orientation: $entity->position()->orientation?->value,
            mitoyennete: $entity->position()->mitoyennete,
        );
    }
}
