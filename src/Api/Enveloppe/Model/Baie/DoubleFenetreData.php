<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\DoubleFenetre as Entity;

final class DoubleFenetreData
{
    public function __construct(
        public ?float $ug,
        public ?float $uw,
        public ?float $sw,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            ug: $entity->data()->ug,
            uw: $entity->data()->uw,
            sw: $entity->data()->sw?->value(),
        );
    }
}
