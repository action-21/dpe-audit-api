<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\PlancherHaut\{Isolation, Position};
use App\Domain\Enveloppe\Entity\{PlancherHaut as Entity, PlancherHautCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\InertieParoi;
use App\Domain\Enveloppe\Enum\PlancherHaut\{Configuration, TypePlancherHaut};
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PlancherHaut
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly Configuration $configuration,

        public readonly ?TypePlancherHaut $type_structure,

        public readonly ?InertieParoi $inertie,

        #[DpeAssert\Annee]
        public readonly ?int $annee_construction,

        #[DpeAssert\Annee]
        public readonly ?int $annee_renovation,

        #[Assert\Positive]
        public readonly ?float $u0,

        #[Assert\Positive]
        public readonly ?float $u,

        #[Assert\Valid]
        public readonly Position $position,

        #[Assert\Valid]
        public readonly Isolation $isolation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            configuration: $entity->configuration(),
            type_structure: $entity->type_structure(),
            inertie: $entity->inertie(),
            annee_construction: $entity->annee_construction()?->value,
            annee_renovation: $entity->annee_renovation()?->value,
            u0: $entity->u0(),
            u: $entity->u(),
            position: Position::from($entity),
            isolation: Isolation::from($entity),
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
