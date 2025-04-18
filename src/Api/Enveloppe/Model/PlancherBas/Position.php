<?php

namespace App\Api\Enveloppe\Model\PlancherBas;

use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Domain\Enveloppe\Entity\PlancherBas as Entity;
use Symfony\Component\Validator\Constraints as Assert;

final class Position
{
    public function __construct(
        #[Assert\Uuid]
        public readonly ?string $local_non_chauffe_id,

        #[Assert\Positive]
        public readonly float $surface,

        #[Assert\Positive]
        public readonly float $perimetre,

        public readonly Mitoyennete $mitoyennete,
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
