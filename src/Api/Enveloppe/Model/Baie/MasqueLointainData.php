<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\MasqueLointain as Entity;

final class MasqueLointainData
{
    public function __construct(public readonly ?float $fe2, public readonly ?float $omb,) {}

    public static function from(Entity $entity): self
    {
        return new self(
            fe2: $entity->data()->fe2,
            omb: $entity->data()->omb,
        );
    }
}
