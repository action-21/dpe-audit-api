<?php

namespace App\Api\Enveloppe\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Entity\{PlancherIntermediaire as Entity, PlancherIntermediaireCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Inertie;

final class PlancherIntermediaireResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly Inertie $inertie,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            inertie: $entity->inertie(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
