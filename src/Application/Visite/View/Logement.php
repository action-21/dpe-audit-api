<?php

namespace App\Application\Visite\View;

use App\Domain\Common\Type\Id;
use App\Domain\Visite\Entity\{Logement as Entity, LogementCollection as EntityCollection};
use App\Domain\Visite\Enum\Typologie;

final class Logement
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Typologie $typologie,
        public readonly float $surface_habitable,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            typologie: $entity->typologie(),
            surface_habitable: $entity->surface_habitable(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
