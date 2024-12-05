<?php

namespace App\Api\Ventilation\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};
use App\Domain\Ventilation\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};

final class GenerateurResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly TypeGenerateur $type,
        public readonly ?TypeVmc $type_vmc,
        public readonly bool $presence_echangeur_thermique,
        public readonly bool $generateur_collectif,
        public readonly ?int $annee_installation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            type_vmc: $entity->type_vmc(),
            presence_echangeur_thermique: $entity->presence_echangeur_thermique(),
            generateur_collectif: $entity->generateur_collectif(),
            annee_installation: $entity->annee_installation(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $item): self => self::from($item))->values();
    }
}
