<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Entity\Lnc\ParoiOpaque as Entity;

final class ParoiOpaqueData
{
    public function __construct(
        public ?float $aue,
        public ?float $aiu,
        public ?EtatIsolation $isolation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            aue: $entity->data()->aue,
            aiu: $entity->data()->aiu,
            isolation: $entity->data()->isolation,
        );
    }
}
