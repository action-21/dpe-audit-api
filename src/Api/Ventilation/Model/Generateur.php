<?php

namespace App\Api\Ventilation\Model;

use App\Domain\Ventilation\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Generateur
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeGenerateur $type,

        public readonly bool $generateur_collectif,

        public readonly ?bool $presence_echangeur_thermique,

        #[DpeAssert\Annee]
        public readonly ?int $annee_installation,

        public readonly ?TypeVmc $type_vmc,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            generateur_collectif: $entity->generateur_collectif(),
            presence_echangeur_thermique: $entity->presence_echangeur_thermique(),
            annee_installation: $entity->annee_installation()?->value,
            type_vmc: $entity->type_vmc(),
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
