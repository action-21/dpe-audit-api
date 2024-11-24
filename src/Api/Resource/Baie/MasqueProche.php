<?php

namespace App\Api\Resource\Baie;

use App\Domain\Baie\Entity\{MasqueProche as Entity, MasqueProcheCollection as EntityCollection};
use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Common\Type\Id;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class MasqueProche
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly string $description,
        public readonly TypeMasqueProche $type,
        public readonly ?float $avancee,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type_masque(),
            avancee: $entity->avancee(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
