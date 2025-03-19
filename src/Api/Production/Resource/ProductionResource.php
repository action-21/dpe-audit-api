<?php

namespace App\Api\Production\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\Production as Entity;
use App\Domain\Production\ValueObject\ProductionPhotovoltaique;

final class ProductionResource
{
    public function __construct(
        public readonly Id $audit_id,
        /** @var PanneauPhotovoltaiqueResource[] */
        public readonly array $panneaux_photovoltaiques,
        /** @var ProductionPhotovoltaique[] */
        public readonly array $productions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            panneaux_photovoltaiques: PanneauPhotovoltaiqueResource::from_collection($entity->panneaux_photovoltaiques()),
            productions: $entity->panneaux_photovoltaiques()->productions()?->values() ?? [],
        );
    }
}
