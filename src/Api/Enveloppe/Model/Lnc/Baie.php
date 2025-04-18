<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Entity\Lnc\{Baie as Entity, BaieCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Lnc\{Materiau, TypeBaie, TypeVitrage};
use Symfony\Component\Validator\Constraints as Assert;

final class Baie
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeBaie $type_baie,

        public readonly ?Materiau $materiau,

        public readonly ?TypeVitrage $type_vitrage,

        public readonly ?bool $presence_rupteur_pont_thermique,

        #[Assert\Valid]
        public readonly PositionBaie $position,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_baie: $entity->type(),
            materiau: $entity->materiau(),
            type_vitrage: $entity->type_vitrage(),
            presence_rupteur_pont_thermique: $entity->presence_rupteur_pont_thermique(),
            position: PositionBaie::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
