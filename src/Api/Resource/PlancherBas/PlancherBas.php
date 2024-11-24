<?php

namespace App\Api\Resource\PlancherBas;

use App\Domain\Common\Type\Id;
use App\Domain\PlancherBas\{PlancherBas as Entity, PlancherBasCollection as EntityCollection};
use App\Domain\PlancherBas\ValueObject\{Caracteristique, Performance, Isolation, Position};
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class PlancherBas
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
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
