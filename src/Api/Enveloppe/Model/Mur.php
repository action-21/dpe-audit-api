<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\Mur\{Isolation, Position};
use App\Domain\Enveloppe\Entity\{Mur as Entity, MurCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\InertieParoi;
use App\Domain\Enveloppe\Enum\Mur\{TypeDoublage, TypeMur};
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Mur
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly ?TypeMur $type_structure,

        #[Assert\Positive]
        public readonly ?float $epaisseur_structure,

        public readonly TypeDoublage $type_doublage,

        public readonly ?bool $presence_enduit_isolant,

        public readonly ?bool $paroi_ancienne,

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
            type_structure: $entity->type_structure(),
            epaisseur_structure: $entity->epaisseur_structure(),
            type_doublage: $entity->type_doublage(),
            presence_enduit_isolant: $entity->presence_enduit_isolant(),
            paroi_ancienne: $entity->paroi_ancienne(),
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
