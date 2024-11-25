<?php

namespace App\Api\Porte\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Porte\{Porte as Entity, PorteCollection as EntityCollection};
use App\Domain\Porte\ValueObject\{Caracteristique, Performance, Position};

final class PorteResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Position $position,
        public readonly Caracteristique $caracteristique,
        public readonly ?Performance $performance,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            position: $entity->position(),
            caracteristique: $entity->caracteristique(),
            performance: $entity->performance(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
