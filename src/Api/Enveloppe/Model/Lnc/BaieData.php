<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Entity\Lnc\Baie as Entity;

final class BaieData
{
    public function __construct(
        public ?float $aue,
        public ?float $aiu,
        public ?EtatIsolation $isolation,
        public ?float $t,
        public ?float $fe,
        public ?float $c1,
        public ?float $sst,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            aue: $entity->data()->aue,
            aiu: $entity->data()->aiu,
            isolation: $entity->data()->isolation,
            t: $entity->data()->ensoleillements?->t(),
            fe: $entity->data()->ensoleillements?->fe(),
            c1: $entity->data()->ensoleillements?->c1(),
            sst: $entity->data()->ensoleillements?->sst(),
        );
    }
}
