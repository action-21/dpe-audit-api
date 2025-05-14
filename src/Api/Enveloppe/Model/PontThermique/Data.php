<?php

namespace App\Api\Enveloppe\Model\PontThermique;

use App\Domain\Enveloppe\Entity\PontThermique as Entity;

final class Data
{
    public function __construct(
        public ?float $kpt,
        public ?float $pt,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            kpt: $entity->data()->kpt,
            pt: $entity->data()->pt,
        );
    }
}
