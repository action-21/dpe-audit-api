<?php

namespace App\Api\Resource\Baie;

use App\Domain\Baie\Entity\{MasqueLointain as Entity, MasqueLointainCollection as EntityCollection};
use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Common\Type\Id;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class MasqueLointain
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly string $description,
        public readonly TypeMasqueLointain $type,
        public readonly float $hauteur,
        public readonly float $orientation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type_masque(),
            hauteur: $entity->hauteur(),
            orientation: $entity->orientation(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
