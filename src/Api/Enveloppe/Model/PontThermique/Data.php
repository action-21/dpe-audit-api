<?php

namespace App\Api\Enveloppe\Model\PontThermique;

use App\Domain\Enveloppe\Entity\PontThermique as Entity;

final class Data
{
    public function __construct(
        public readonly ?float $kpt,
        public readonly ?float $pt,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            kpt: $entity->data()->kpt,
            pt: $entity->data()->pt,
        );
    }
}
