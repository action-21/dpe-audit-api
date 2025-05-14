<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Entity\Lnc as Entity;

final class Data
{
    public function __construct(
        public ?float $aiu,
        public ?float $aue,
        public ?EtatIsolation $isolation_aiu,
        public ?EtatIsolation $isolation_aue,
        public ?float $uvue,
        public ?float $b,
        public ?float $bver,
        public ?float $t,
        public ?float $fe,
        public ?float $c1,
        public ?float $sst,
        public ?float $ssd,
        public ?float $ssind,
        public ?float $sse,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            aue: $entity->data()->aue,
            aiu: $entity->data()->aiu,
            isolation_aiu: $entity->data()->isolation_aiu,
            isolation_aue: $entity->data()->isolation_aue,
            uvue: $entity->data()->uvue,
            b: $entity->data()->b,
            bver: $entity->data()->bver,
            t: $entity->data()->ensoleillements?->t(),
            fe: $entity->data()->ensoleillements?->fe(),
            c1: $entity->data()->ensoleillements?->c1(),
            sst: $entity->data()->ensoleillements?->sst(),
            ssd: $entity->data()->ensoleillements?->ssd(),
            ssind: $entity->data()->ensoleillements?->ssind(),
            sse: $entity->data()->ensoleillements?->sse(),
        );
    }
}
