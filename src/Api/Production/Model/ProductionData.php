<?php

namespace App\Api\Production\Model;

use App\Domain\Production\Production as Entity;

final class ProductionData
{
    public function __construct(
        public readonly ?float $production,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            production: $entity->data()->production,
        );
    }
}
