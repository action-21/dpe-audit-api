<?php

namespace App\Api\Enveloppe\Model\Mur;

use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance};
use App\Domain\Enveloppe\Entity\Mur as Entity;

final class Data
{
    public function __construct(
        public readonly ?float $sdep,
        public readonly ?float $u0,
        public readonly ?float $u,
        public readonly ?float $b,
        public readonly ?float $dp,
        public readonly ?EtatIsolation $isolation,
        public readonly ?Performance $performance,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            sdep: $entity->data()->sdep,
            u0: $entity->data()->u0,
            u: $entity->data()->u,
            b: $entity->data()->b,
            dp: $entity->data()->dp,
            isolation: $entity->data()->isolation,
            performance: $entity->data()->performance,
        );
    }
}
