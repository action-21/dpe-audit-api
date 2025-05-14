<?php

namespace App\Api\Enveloppe\Model\PlancherBas;

use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance};
use App\Domain\Enveloppe\Entity\PlancherBas as Entity;

final class Data
{
    public function __construct(
        public ?float $sdep,
        public ?float $u0,
        public ?float $u,
        public ?float $b,
        public ?float $dp,
        public ?EtatIsolation $isolation,
        public ?Performance $performance,
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
