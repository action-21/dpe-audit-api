<?php

namespace App\Api\Refroidissement\Model;

use App\Domain\Refroidissement\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class Systeme
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        #[Assert\Uuid]
        public readonly string $installation_id,

        #[Assert\Uuid]
        public readonly string $generateur_id,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            installation_id: $entity->installation()->id(),
            generateur_id: $entity->generateur()?->id(),
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
