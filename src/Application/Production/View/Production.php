<?php

namespace App\Application\Production\View;

use App\Domain\Common\Type\Id;
use App\Domain\Production\Production as Entity;
use App\Domain\Production\ValueObject\ProductionPhotovoltaique;

/**
 * @property PanneauPhotovoltaique[] $panneaux_photovoltaiques
 * @property ProductionPhotovoltaique[] $productions
 */
final class Production
{
    public function __construct(
        public readonly Id $audit_id,
        public readonly array $panneaux_photovoltaiques,
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
