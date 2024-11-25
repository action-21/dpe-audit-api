<?php

namespace App\Api\Refroidissement\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};

final class GenerateurResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly TypeGenerateur $type_generateur,
        public readonly EnergieGenerateur $energie_generateur,
        public readonly ?int $annee_installation,
        public readonly ?float $seer,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_generateur: $entity->type_generateur(),
            energie_generateur: $entity->energie(),
            annee_installation: $entity->annee_installation(),
            seer: $entity->seer(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
