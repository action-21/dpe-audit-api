<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\{MasqueProche as Entity, MasqueProcheCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueProche;
use Symfony\Component\Validator\Constraints as Assert;

final class MasqueProche
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeMasqueProche $type_masque,

        #[Assert\Positive]
        public readonly ?float $profondeur,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_masque: $entity->type_masque(),
            profondeur: $entity->profondeur(),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
