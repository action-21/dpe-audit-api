<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Entity\Lnc\Baie as Entity;

final class BaieData
{
    public function __construct(
        public readonly ?float $aue,
        public readonly ?float $aiu,
        public readonly ?EtatIsolation $isolation,
        public readonly ?float $t,
        public readonly ?float $fe,
        public readonly ?float $c1,
        public readonly ?float $sst,
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
