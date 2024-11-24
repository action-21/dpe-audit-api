<?php

namespace App\Api\Resource\Production;

use App\Domain\Common\Type\Id;
use App\Domain\Production\Production as Entity;
use App\Domain\Production\ValueObject\ProductionPhotovoltaique;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Production
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $audit_id,
        /** @var PanneauPhotovoltaique[] */
        public readonly array $panneaux_photovoltaiques,
        /** @var ProductionPhotovoltaique[] */
        public readonly array $productions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            panneaux_photovoltaiques: PanneauPhotovoltaique::from_collection($entity->panneaux_photovoltaiques()),
            productions: $entity->panneaux_photovoltaiques()->productions()?->values() ?? [],
        );
    }
}
