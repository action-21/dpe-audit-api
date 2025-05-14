<?php

namespace App\Api\Enveloppe\Model\PlancherBas;

use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Domain\Enveloppe\Entity\PlancherBas as Entity;
use Symfony\Component\Validator\Constraints as Assert;

final class Position
{
    public function __construct(
        public ?string $local_non_chauffe_id,

        #[Assert\Positive]
        public float $surface,

        #[Assert\Positive]
        public float $perimetre,

        public Mitoyennete $mitoyennete,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            local_non_chauffe_id: $entity->position()->local_non_chauffe?->id(),
            surface: $entity->position()->surface,
            perimetre: $entity->position()->perimetre,
            mitoyennete: $entity->position()->mitoyennete,
        );
    }
}
