<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Entity\Lnc\{Baie as Entity, BaieCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Lnc\{Materiau, TypeBaie, TypeVitrage};
use Symfony\Component\Validator\Constraints as Assert;

final class Baie
{
    public function __construct(
        public string $id,

        public string $description,

        public TypeBaie $type_baie,

        public ?Materiau $materiau,

        public ?TypeVitrage $type_vitrage,

        public ?bool $presence_rupteur_pont_thermique,

        #[Assert\Valid]
        public PositionBaie $position,

        public ?BaieData $data,
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
            data: BaieData::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
