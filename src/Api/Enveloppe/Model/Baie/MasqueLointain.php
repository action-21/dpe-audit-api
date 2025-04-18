<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\{MasqueLointain as Entity, MasqueLointainCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueLointain;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class MasqueLointain
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeMasqueLointain $type_masque,

        #[Assert\Positive]
        public readonly float $hauteur,

        #[DpeAssert\Orientation]
        public readonly float $orientation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_masque: $entity->type_masque(),
            hauteur: $entity->hauteur(),
            orientation: $entity->orientation()->value,
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
