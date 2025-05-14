<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance};
use App\Domain\Enveloppe\Entity\Baie as Entity;

final class Data
{
    public function __construct(
        public ?float $sdep,
        public ?float $ug,
        public ?float $uw,
        public ?float $u,
        public ?float $b,
        public ?float $dp,
        public ?Performance $performance,
        public ?EtatIsolation $isolation,
        public ?float $sw,
        public ?float $fe,
        public ?float $c1,
        public ?float $sse,
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
