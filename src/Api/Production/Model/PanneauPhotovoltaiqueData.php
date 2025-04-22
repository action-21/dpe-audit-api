<?php

namespace App\Api\Production\Model;

use App\Domain\Production\Entity\PanneauPhotovoltaique as Entity;

final class PanneauPhotovoltaiqueData
{
    public function __construct(
        public readonly ?float $kpv,
        public readonly ?float $surface,
        public readonly ?float $production,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            kpv: $entity->data()->kpv,
            surface: $entity->data()->surface,
            production: $entity->data()->production,
        );
    }
}
