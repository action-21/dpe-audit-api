<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Entity\Lnc as Entity;

final class Data
{
    public function __construct(
        public readonly ?float $aiu,
        public readonly ?float $aue,
        public readonly ?EtatIsolation $isolation_aiu,
        public readonly ?EtatIsolation $isolation_aue,
        public readonly ?float $uvue,
        public readonly ?float $b,
        public readonly ?float $bver,
        public readonly ?float $t,
        public readonly ?float $fe,
        public readonly ?float $c1,
        public readonly ?float $sst,
        public readonly ?float $ssd,
        public readonly ?float $ssind,
        public readonly ?float $sse,
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
