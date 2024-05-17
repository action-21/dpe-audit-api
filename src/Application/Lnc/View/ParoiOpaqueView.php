<?php

namespace App\Application\Lnc\View;

use App\Domain\Lnc\Entity\{ParoiOpaque, ParoiOpaqueCollection};

class ParoiOpaqueView
{
    public function __construct(
        public readonly string $description,
        public readonly float $surface,
        public readonly bool $isolation,
    ) {
    }

    public static function from_entity(ParoiOpaque $entity): self
    {
        return new self(
            description: $entity->description(),
            surface: $entity->surface()->valeur(),
            isolation: $entity->isolation(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(ParoiOpaqueCollection $collection): array
    {
        return \array_map(fn (ParoiOpaque $entity) => self::from_entity($entity), $collection->to_array());
    }
}
