<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance};
use App\Domain\Enveloppe\Entity\Baie as Entity;

final class Data
{
    public function __construct(
        public readonly ?float $sdep,
        public readonly ?float $ug,
        public readonly ?float $uw,
        public readonly ?float $u,
        public readonly ?float $b,
        public readonly ?float $dp,
        public readonly ?Performance $performance,
        public readonly ?EtatIsolation $isolation,
        public readonly ?float $sw,
        public readonly ?float $fe,
        public readonly ?float $c1,
        public readonly ?float $sse,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            sdep: $entity->data()->sdep,
            ug: $entity->data()->ug,
            uw: $entity->data()->uw,
            u: $entity->data()->u,
            b: $entity->data()->b,
            dp: $entity->data()->dp,
            isolation: $entity->data()->isolation,
            performance: $entity->data()->performance,
            sw: $entity->data()->ensoleillements?->sw() ?? null,
            fe: $entity->data()->ensoleillements?->fe() ?? null,
            c1: $entity->data()->ensoleillements?->c1() ?? null,
            sse: $entity->data()->ensoleillements?->sse() ?? null,
        );
    }
}
