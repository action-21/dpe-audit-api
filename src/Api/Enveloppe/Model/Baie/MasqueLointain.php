<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\{MasqueLointain as Entity, MasqueLointainCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueLointain;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class MasqueLointain
{
    public function __construct(
        public string $id,

        public string $description,

        public TypeMasqueLointain $type_masque,

        #[Assert\Positive]
        public float $hauteur,

        #[DpeAssert\Orientation]
        public float $orientation,

        public ?MasqueLointainData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_masque: $entity->type_masque(),
            hauteur: $entity->hauteur(),
            orientation: $entity->orientation()->value,
            data: MasqueLointainData::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
