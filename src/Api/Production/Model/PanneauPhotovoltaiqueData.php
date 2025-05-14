<?php

namespace App\Api\Production\Model;

use App\Domain\Production\Entity\PanneauPhotovoltaique as Entity;

final class PanneauPhotovoltaiqueData
{
    public function __construct(
        public ?float $kpv,
        public ?float $surface,
        public ?float $production,
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
