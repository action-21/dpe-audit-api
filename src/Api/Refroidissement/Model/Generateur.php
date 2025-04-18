<?php

namespace App\Api\Refroidissement\Model;

use App\Domain\Refroidissement\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur, TypeVmc};
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Generateur
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeGenerateur $type,

        public readonly EnergieGenerateur $energie,

        #[DpeAssert\Annee]
        public readonly ?int $annee_installation,

        #[Assert\Positive]
        public readonly ?float $seer,

        #[Assert\Uuid]
        public readonly ?string $reseau_froid_id,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            energie: $entity->energie(),
            annee_installation: $entity->annee_installation()?->value,
            seer: $entity->seer(),
            reseau_froid_id: $entity->reseau_froid()?->id(),
        );
    }

    /**
     * @return self[]
     */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
