<?php

namespace App\Api\Chauffage\Model;

use App\Domain\Chauffage\Enum\TypeChauffage;
use App\Domain\Chauffage\Entity\{Emetteur as EmetteurEntity, Systeme as Entity, SystemeCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property string[] $emetteurs
 */
final class Systeme
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        #[Assert\Uuid]
        public readonly string $installation_id,

        #[Assert\Uuid]
        public readonly string $generateur_id,

        public readonly TypeChauffage $type,

        #[Assert\Valid]
        public readonly ?Reseau $reseau,

        #[Assert\All([
            new Assert\Type('string'),
            new Assert\Uuid,
        ])]
        public readonly array $emetteurs,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            installation_id: $entity->installation()->id(),
            generateur_id: $entity->generateur()?->id(),
            type: $entity->type_chauffage(),
            reseau: Reseau::from($entity),
            emetteurs: $entity->emetteurs()->map(fn(EmetteurEntity $item) => $item->id()->value)->to_array(),
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
