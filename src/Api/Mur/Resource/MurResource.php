<?php

namespace App\Api\Mur\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Mur\{Mur as Entity, MurCollection as EntityCollection};
use App\Domain\Mur\ValueObject\{Caracteristique, Performance, Isolation, Position};

final class MurResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Position $position,
        public readonly Caracteristique $caracteristique,
        public readonly Isolation $isolation,
        public readonly ?float $surface_deperditive,
        public readonly ?Performance $performance,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            position: $entity->position(),
            caracteristique: $entity->caracteristique(),
            isolation: $entity->isolation(),
            surface_deperditive: $entity->surface_deperditive(),
            performance: $entity->performance(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
